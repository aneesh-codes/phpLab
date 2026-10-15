<!-- Write a PHP program to create an associative array containing student names and their
marks. Use appropriate built-in array functions to display the keys and values, search for a
specified student, sort the records by student name in ascending and descending order, and
sort the records by marks in ascending and descending order. -->

<?php
$students = [
    "ABC" => 85,
    "XYZ" => 75,
    "ABD" => 95,
    "PQR" => 80
];
// builtin array functions to display the keys and values
echo "Keys (Student Names): ";
echo implode(", ", array_keys($students));

echo "<br> Values (Marks):";
echo implode(", ", array_values($students));

// sorting the records by student name in ascending order
asort($students);
echo "<br> Sorted by Student Name (Ascending): ";
echo implode(", ", array_keys($students));

// sorting the marks in ascending order
asort($students);
echo "<br> Sorted by Marks (Ascending): ";
echo implode(", ", array_values($students));

// searching for a specified student

$search_student = "ABD";
if(array_key_exists($search_student, $students)){
    echo "<br> Student '$search_student' found with marks: " . $students[$search_student];
} else {
    echo "<br> Student '$search_student' not found.";
}