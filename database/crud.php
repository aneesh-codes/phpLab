<?php
$server = "localhost";
$username = "root";
$password = "";
$dbName = "phplab";

$conn = mysqli_connect($server, $username, $password, $dbName);

if (!$conn) {
    $conn = mysqli_connect($server, $username, $password);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $createDb = "CREATE DATABASE IF NOT EXISTS `$dbName`";
    if (!mysqli_query($conn, $createDb)) {
        die("Database creation failed: " . mysqli_error($conn));
    }

    $conn = mysqli_connect($server, $username, $password, $dbName);
    if (!$conn) {
        die("Failed to connect to database '$dbName': " . mysqli_connect_error());
    }
}

mysqli_set_charset($conn, "utf8mb4");

$createCourses = "
    CREATE TABLE IF NOT EXISTS courses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        duration VARCHAR(50) NOT NULL,
        status ENUM('Active', 'Inactive') DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
";

$createStudents = "
    CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        course_id INT NOT NULL,
        fee DECIMAL(10,2) NOT NULL,
        rollno VARCHAR(50) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        address TEXT NOT NULL,
        dob DATE NOT NULL,
        status ENUM('Active', 'Inactive') DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT fk_course FOREIGN KEY (course_id) REFERENCES courses(id)
            ON DELETE CASCADE ON UPDATE CASCADE
    )
";

mysqli_query($conn, $createCourses) or die("Error creating courses table: " . mysqli_error($conn));
mysqli_query($conn, $createStudents) or die("Error creating students table: " . mysqli_error($conn));

$courseMessage = "";
$studentMessage = "";
$editCourse = null;
$editStudent = null;

$tableExists = function ($tableName) use ($conn) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$tableName'");
    return $result && mysqli_num_rows($result) > 0;
};

if (!$tableExists('courses') || !$tableExists('students')) {
    mysqli_query($conn, $createCourses);
    mysqli_query($conn, $createStudents);
}

if (isset($_GET['delete_course'])) {
    $courseId = (int) $_GET['delete_course'];
    $deleteCourseSql = "DELETE FROM courses WHERE id = $courseId";
    if (mysqli_query($conn, $deleteCourseSql)) {
        $courseMessage = "Course deleted successfully.";
    } else {
        $courseMessage = "Delete failed: " . mysqli_error($conn);
    }
}

if (isset($_GET['delete_student'])) {
    $studentId = (int) $_GET['delete_student'];
    $deleteStudentSql = "DELETE FROM students WHERE id = $studentId";
    if (mysqli_query($conn, $deleteStudentSql)) {
        $studentMessage = "Student deleted successfully.";
    } else {
        $studentMessage = "Delete failed: " . mysqli_error($conn);
    }
}

if (isset($_GET['edit_course'])) {
    $courseId = (int) $_GET['edit_course'];
    $result = mysqli_query($conn, "SELECT * FROM courses WHERE id = $courseId");
    if ($result && mysqli_num_rows($result) > 0) {
        $editCourse = mysqli_fetch_assoc($result);
    }
}

if (isset($_GET['edit_student'])) {
    $studentId = (int) $_GET['edit_student'];
    $result = mysqli_query($conn, "SELECT * FROM students WHERE id = $studentId");
    if ($result && mysqli_num_rows($result) > 0) {
        $editStudent = mysqli_fetch_assoc($result);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_course'])) {
        $courseId = isset($_POST['course_id']) && $_POST['course_id'] !== '' ? (int) $_POST['course_id'] : null;
        $title = mysqli_real_escape_string($conn, trim($_POST['title']));
        $duration = mysqli_real_escape_string($conn, trim($_POST['duration']));
        $status = mysqli_real_escape_string($conn, $_POST['status']);

        if ($courseId) {
            $sql = "UPDATE courses SET title='$title', duration='$duration', status='$status' WHERE id=$courseId";
            $message = "Course updated successfully.";
        } else {
            $sql = "INSERT INTO courses (title, duration, status) VALUES ('$title', '$duration', '$status')";
            $message = "Course added successfully.";
        }

        if (mysqli_query($conn, $sql)) {
            $courseMessage = $message;
        } else {
            $courseMessage = "Course operation failed: " . mysqli_error($conn);
        }
    }

    if (isset($_POST['save_student'])) {
        $studentId = isset($_POST['student_id']) && $_POST['student_id'] !== '' ? (int) $_POST['student_id'] : null;
        $name = mysqli_real_escape_string($conn, trim($_POST['name']));
        $courseId = (int) $_POST['course_id'];
        $fee = mysqli_real_escape_string($conn, trim($_POST['fee']));
        $rollno = mysqli_real_escape_string($conn, trim($_POST['rollno']));
        $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
        $address = mysqli_real_escape_string($conn, trim($_POST['address']));
        $dob = mysqli_real_escape_string($conn, trim($_POST['dob']));
        $status = mysqli_real_escape_string($conn, $_POST['status']);

        if ($studentId) {
            $sql = "UPDATE students SET name='$name', course_id=$courseId, fee='$fee', rollno='$rollno', phone='$phone', address='$address', dob='$dob', status='$status' WHERE id=$studentId";
            $message = "Student updated successfully.";
        } else {
            $sql = "INSERT INTO students (name, course_id, fee, rollno, phone, address, dob, status) VALUES ('$name', $courseId, '$fee', '$rollno', '$phone', '$address', '$dob', '$status')";
            $message = "Student added successfully.";
        }

        if (mysqli_query($conn, $sql)) {
            $studentMessage = $message;
        } else {
            $studentMessage = "Student operation failed: " . mysqli_error($conn);
        }
    }
}

$coursesResult = mysqli_query($conn, "SELECT * FROM courses ORDER BY id DESC");
$studentsResult = mysqli_query($conn, "SELECT * FROM students ORDER BY id DESC");
$courseOptions = mysqli_query($conn, "SELECT id, title FROM courses ORDER BY title");

$courseNames = [];
if ($courseOptions) {
    while ($courseRow = mysqli_fetch_assoc($courseOptions)) {
        $courseNames[$courseRow['id']] = $courseRow['title'];
    }
    mysqli_data_seek($courseOptions, 0);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Operations</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1100px;
            margin: auto;
        }
        h2 {
            color: #333;
        }
        .form-box {
            background: #fff;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input, select, textarea, button {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        textarea {
            min-height: 80px;
            resize: vertical;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background: #0056b3;
        }
        .message {
            padding: 10px 15px;
            background: #e8f5e9;
            color: #1b5e20;
            border: 1px solid #a5d6a7;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #f0f0f0;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        .actions a {
            margin-right: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Course Management</h2>

    <?php if ($courseMessage): ?>
        <div class="message"><?php echo htmlspecialchars($courseMessage); ?></div>
    <?php endif; ?>

    <div class="form-box">
        <form method="POST">
            <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($editCourse['id'] ?? ''); ?>">

            <div>
                <label>Title</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($editCourse['title'] ?? ''); ?>" required>
            </div>

            <div>
                <label>Duration</label>
                <input type="text" name="duration" value="<?php echo htmlspecialchars($editCourse['duration'] ?? ''); ?>" required>
            </div>

            <div>
                <label>Status</label>
                <select name="status">
                    <option value="Active" <?php echo (($editCourse['status'] ?? 'Active') === 'Active') ? 'selected' : ''; ?>>Active</option>
                    <option value="Inactive" <?php echo (($editCourse['status'] ?? 'Active') === 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>

            <div style="align-self: end;">
                <button type="submit" name="save_course"><?php echo $editCourse ? 'Update Course' : 'Add Course'; ?></button>
            </div>

            <?php if ($editCourse): ?>
                <div style="align-self: end;">
                    <a href="crud.php"><button type="button">Cancel</button></a>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Duration</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($course = mysqli_fetch_assoc($coursesResult)): ?>
            <tr>
                <td><?php echo htmlspecialchars($course['id']); ?></td>
                <td><?php echo htmlspecialchars($course['title']); ?></td>
                <td><?php echo htmlspecialchars($course['duration']); ?></td>
                <td><?php echo htmlspecialchars($course['status']); ?></td>
                <td><?php echo htmlspecialchars($course['created_at']); ?></td>
                <td><?php echo htmlspecialchars($course['updated_at']); ?></td>
                <td class="actions">
                    <a href="crud.php?edit_course=<?php echo $course['id']; ?>">Edit</a>
                    <a href="crud.php?delete_course=<?php echo $course['id']; ?>" onclick="return confirm('Delete this course?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <h2>Student Management</h2>

    <?php if ($studentMessage): ?>
        <div class="message"><?php echo htmlspecialchars($studentMessage); ?></div>
    <?php endif; ?>

    <div class="form-box">
        <form method="POST">
            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($editStudent['id'] ?? ''); ?>">

            <div>
                <label>Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($editStudent['name'] ?? ''); ?>" required>
            </div>

            <div>
                <label>Course</label>
                <select name="course_id" required>
                    <option value="">Select Course</option>
                    <?php if ($courseOptions) {
                        while ($option = mysqli_fetch_assoc($courseOptions)): ?>
                            <option value="<?php echo $option['id']; ?>" <?php echo (($editStudent['course_id'] ?? '') == $option['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($option['title']); ?>
                            </option>
                        <?php endwhile;
                        mysqli_data_seek($courseOptions, 0);
                    } ?>
                </select>
            </div>

            <div>
                <label>Fee</label>
                <input type="number" step="0.01" name="fee" value="<?php echo htmlspecialchars($editStudent['fee'] ?? ''); ?>" required>
            </div>

            <div>
                <label>Roll No</label>
                <input type="text" name="rollno" value="<?php echo htmlspecialchars($editStudent['rollno'] ?? ''); ?>" required>
            </div>

            <div>
                <label>Phone</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($editStudent['phone'] ?? ''); ?>" required>
            </div>

            <div>
                <label>Date of Birth</label>
                <input type="date" name="dob" value="<?php echo htmlspecialchars($editStudent['dob'] ?? ''); ?>" required>
            </div>

            <div>
                <label>Status</label>
                <select name="status">
                    <option value="Active" <?php echo (($editStudent['status'] ?? 'Active') === 'Active') ? 'selected' : ''; ?>>Active</option>
                    <option value="Inactive" <?php echo (($editStudent['status'] ?? 'Active') === 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>

            <div>
                <label>Address</label>
                <textarea name="address" required><?php echo htmlspecialchars($editStudent['address'] ?? ''); ?></textarea>
            </div>

            <div style="align-self: end;">
                <button type="submit" name="save_student"><?php echo $editStudent ? 'Update Student' : 'Add Student'; ?></button>
            </div>

            <?php if ($editStudent): ?>
                <div style="align-self: end;">
                    <a href="crud.php"><button type="button">Cancel</button></a>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Course</th>
                <th>Fee</th>
                <th>Roll No</th>
                <th>Phone</th>
                <th>DOB</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($student = mysqli_fetch_assoc($studentsResult)): ?>
            <tr>
                <td><?php echo htmlspecialchars($student['id']); ?></td>
                <td><?php echo htmlspecialchars($student['name']); ?></td>
                <td><?php echo htmlspecialchars($courseNames[$student['course_id']] ?? 'Unknown'); ?></td>
                <td><?php echo htmlspecialchars($student['fee']); ?></td>
                <td><?php echo htmlspecialchars($student['rollno']); ?></td>
                <td><?php echo htmlspecialchars($student['phone']); ?></td>
                <td><?php echo htmlspecialchars($student['dob']); ?></td>
                <td><?php echo htmlspecialchars($student['status']); ?></td>
                <td class="actions">
                    <a href="crud.php?edit_student=<?php echo $student['id']; ?>">Edit</a>
                    <a href="crud.php?delete_student=<?php echo $student['id']; ?>" onclick="return confirm('Delete this student?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php mysqli_close($conn); ?>
