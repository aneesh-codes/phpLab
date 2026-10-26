<!-- Write a PHP and MySQL program to retrieve student marks from a database, calculate
total marks, percentage, and grade, and display the result in a formatted mark sheet. -->

<?php
$server = "localhost";
$username = "root";
$password = "";
$dbName = "phplab";

$conn = mysqli_connect($server, $username, $password, $dbName);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// retrieve student marks from the database
$query = "SELECT * FROM student_marks";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// display the mark sheet
echo "<h2> Student Mark Sheet </h2>";
echo "<table border ='1'>";
echo "<tr><th>Student ID</th><th>Student Name</th><th>Subject 1</th><th>Subject 2</th><th>Subject 3</th><th>Total Marks</th><th>Percentage</th><th>Grade</th></tr>";
while ($row = mysqli_fetch_assoc($result)) {
    $totalMarks = $row['subject1'] + $row['subject2'] + $row['subject3'];
    $percent = ($totalMarks / 300) * 100;
    if ($percent >= 90){
        $grade = "A+";
     }else if ($percent >= 80){
        $grade = "A";
     }else if ($percent >= 70){
        $grade = "B";
     }else if ($percent >= 60){
        $grade = "C";
     } else if ($percent >= 50){
        $grade = "D";
     } else {
        $grade = "F";
     }
     echo "<tr>";
     echo "<td>" . $row['student_id'] . "</td>";
     echo "<td>" . $row['student_name'] . "</td>";
        echo "<td>" . $row['subject1'] . "</td>";
        echo "<td>" . $row['subject2'] . "</td>";
        echo "<td>" . $row['subject3'] . "</td>";
        echo "<td>" . $totalMarks . "</td>";
        echo "<td>" . $percent . "%</td>";
        echo "<td>" . $grade . "</td>";
}
echo "</table>";

mysqli_close($conn);
