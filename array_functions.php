<!-- Write a PHP program to create an array containing the names of 10 students and use
appropriate built-in array functions to count the elements, search for a specified student,
sort the names in ascending and descending order, reverse the array, add new elements,
remove elements, and remove duplicate values. Display the array after each major
operation. -->

<?php
// Create an array of student names
$students =['Anish','Aayush','Saksham','Rahul','Rohit','Saksham','Anish','Ansh','Rohit','Rajit'];
echo "Original array: " . implode(", ", $students) . "<br>";

// arrayfunctions#
    // Count the number of elements in the array
    $count = count($students);
    echo "Number of students: $count <br>";

// Search for a specified student
$search_student = $_POST['student_name'] ?? '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (in_array($search_student, $students)) {
        echo "<p>$search_student is present in the array.</p>";
    } else {
        echo "<p>$search_student is not present in the array.</p>";
    }
}
?>

<form method="POST">
    <label>Enter the name of the student to search for:</label><br>
    <input type="text" name="student_name"><br><br>
    <input type="submit" value="Search">
</form>

<!-- sorting the array -->
 <?php
  
    // Sort the array in ascending order
    sort($students);
    echo "Sorted array in ascending order:<br> " . implode(", ", $students) . "<br>";

    // Sort the array in descending order
    rsort($students);
    echo "Sorted array in descending order:<br> " . implode(", ", $students) . "<br>";

    // Reverse the array
    $reversed = array_reverse($students);
    echo "Reversed array:<br> " . implode(", ", $reversed) . "<br>";

    // Add new elements to the array
    $new_students = ['Amit', 'Suresh'];
    $students = array_merge($students, $new_students);
    echo "Array after adding new students:<br> " . implode(", ", $students) . "<br>";

    // Remove elements from the array
    unset($students[0]); // Remove the first element
    echo "Array after removing the first student:<br> " . implode(", ", $students) . "<br>";

    // Remove duplicate values from the array
    $unique_students = array_unique($students);
    echo "Array after removing duplicates:<br> " . implode(", ", $unique_students) . "<br>";
?>
