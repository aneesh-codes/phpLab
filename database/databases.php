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

$tableSql = "CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    rank INT NOT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    image VARCHAR(255) DEFAULT NULL,
    created_by VARCHAR(100) NOT NULL,
    updated_by VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (!mysqli_query($conn, $tableSql)) {
    die("Table creation failed: " . mysqli_error($conn));
}

function sanitize($data)
{
    return trim(htmlspecialchars($data, ENT_QUOTES, 'UTF-8'));
}

$errors = [];
$success = "";
$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $rank = trim($_POST['rank'] ?? '');
    $status = $_POST['status'] ?? 'active';
    $createdBy = trim($_POST['created_by'] ?? '');
    $updatedBy = trim($_POST['updated_by'] ?? '');
    $imageName = "";

    if ($name === '') {
        $errors[] = 'Name is required.';
    } elseif (!preg_match('/^[A-Za-z .\'-]+$/', $name)) {
        $errors[] = 'Name can contain only letters, spaces, and punctuation.';
    }

    if ($rank === '') {
        $errors[] = 'Rank is required.';
    } elseif (!ctype_digit($rank) || (int) $rank <= 0) {
        $errors[] = 'Rank must be a positive integer.';
    }

    if (!in_array($status, ['active', 'inactive'], true)) {
        $errors[] = 'Invalid status selected.';
    }

    if ($createdBy === '') {
        $errors[] = 'Created by is required.';
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileName = $_FILES['image']['name'];
        $fileTmp = $_FILES['image']['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileExt, $allowed, true)) {
            $errors[] = 'Only JPG, JPEG, PNG, and GIF images are allowed.';
        } else {
            $imageName = time() . '_' . basename($fileName);
            $uploadDir = __DIR__ . '/../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $targetPath = $uploadDir . $imageName;
            if (!move_uploaded_file($fileTmp, $targetPath)) {
                $errors[] = 'Image upload failed.';
            }
        }
    }

    if (empty($errors)) {
        $name = mysqli_real_escape_string($conn, $name);
        $status = mysqli_real_escape_string($conn, $status);
        $createdBy = mysqli_real_escape_string($conn, $createdBy);
        $updatedBy = mysqli_real_escape_string($conn, $updatedBy === '' ? $createdBy : $updatedBy);
        $rank = (int) $rank;

        if (isset($_POST['edit_id']) && $_POST['edit_id'] !== '') {
            $studentId = (int) $_POST['edit_id'];

            if ($imageName === '') {
                $stmt = mysqli_prepare($conn, "UPDATE students SET name=?, rank=?, status=?, updated_by=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, 'sissi', $name, $rank, $status, $updatedBy, $studentId);
            } else {
                $stmt = mysqli_prepare($conn, "UPDATE students SET name=?, rank=?, status=?, image=?, updated_by=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, 'sisssi', $name, $rank, $status, $imageName, $updatedBy, $studentId);
            }

            if (mysqli_stmt_execute($stmt)) {
                $success = 'Student record updated successfully.';
            } else {
                $errors[] = 'Update failed: ' . mysqli_error($conn);
            }
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO students (name, rank, status, image, created_by, updated_by) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'sissss', $name, $rank, $status, $imageName, $createdBy, $createdBy);

            if (mysqli_stmt_execute($stmt)) {
                $success = 'Student record inserted successfully.';
            } else {
                $errors[] = 'Insertion failed: ' . mysqli_error($conn);
            }
        }
    }
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];
    $deleteStmt = mysqli_prepare($conn, "DELETE FROM students WHERE id = ?");
    mysqli_stmt_bind_param($deleteStmt, 'i', $deleteId);

    if (mysqli_stmt_execute($deleteStmt)) {
        $success = 'Student record deleted successfully.';
    } else {
        $errors[] = 'Deletion failed: ' . mysqli_error($conn);
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$editData = ['name' => '', 'rank' => '', 'status' => 'active', 'created_by' => '', 'updated_by' => ''];

if ($editId > 0) {
    $result = mysqli_query($conn, "SELECT * FROM students WHERE id = $editId");
    if ($row = mysqli_fetch_assoc($result)) {
        $editData = [
            'name' => $row['name'],
            'rank' => $row['rank'],
            'status' => $row['status'],
            'created_by' => $row['created_by'],
            'updated_by' => $row['updated_by']
        ];
    }
}

$students = mysqli_query($conn, "SELECT * FROM students ORDER BY id DESC");
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Records</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f6f8fa; }
        .container { max-width: 1000px; margin: auto; }
        .form-box, .table-box { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 25px; }
        .row { display: flex; gap: 15px; flex-wrap: wrap; }
        .field { flex: 1 1 220px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        input, select, button { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; }
        button { background: #2d7ff9; color: #fff; border: none; cursor: pointer; }
        .danger { background: #d9534f; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f2f2f2; }
        img { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; }
        .actions a { margin-right: 10px; text-decoration: none; }
    </style>
</head>
<body>
<div class="container">
    <h2>Student Record Management</h2>

    <div class="form-box">
        <?php if ($success !== ''): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <ul class="error">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
            <?php if ($editId > 0): ?>
                <input type="hidden" name="edit_id" value="<?php echo $editId; ?>">
            <?php endif; ?>

            <div class="row">
                <div class="field">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?php echo sanitize($editData['name']); ?>" required>
                </div>

                <div class="field">
                    <label for="rank">Rank</label>
                    <input type="number" id="rank" name="rank" min="1" value="<?php echo sanitize($editData['rank']); ?>" required>
                </div>

                <div class="field">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="active" <?php echo ($editData['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($editData['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="row" style="margin-top:15px;">
                <div class="field">
                    <label for="created_by">Created By</label>
                    <input type="text" id="created_by" name="created_by" value="<?php echo sanitize($editData['created_by']); ?>" required>
                </div>

                <div class="field">
                    <label for="updated_by">Updated By</label>
                    <input type="text" id="updated_by" name="updated_by" value="<?php echo sanitize($editData['updated_by']); ?>">
                </div>

                <div class="field">
                    <label for="image">Image</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>
            </div>

            <div style="margin-top:15px;">
                <button type="submit"><?php echo ($editId > 0) ? 'Update Student' : 'Save Student'; ?></button>
            </div>
        </form>
    </div>

    <div class="table-box">
        <h3>Student List</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Rank</th>
                    <th>Status</th>
                    <th>Image</th>
                    <th>Created By</th>
                    <th>Updated By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($students) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($students)): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo sanitize($row['name']); ?></td>
                            <td><?php echo $row['rank']; ?></td>
                            <td><?php echo sanitize($row['status']); ?></td>
                            <td>
                                <?php if (!empty($row['image'])): ?>
                                    <img src="../uploads/<?php echo sanitize($row['image']); ?>" alt="Student Image">
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?php echo sanitize($row['created_by']); ?></td>
                            <td><?php echo sanitize($row['updated_by'] ?? ''); ?></td>
                            <td class="actions">
                                <a href="<?php echo $_SERVER['PHP_SELF']; ?>?edit=<?php echo $row['id']; ?>">Edit</a>
                                <a href="<?php echo $_SERVER['PHP_SELF']; ?>?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No student records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
