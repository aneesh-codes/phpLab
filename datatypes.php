<?php
$name     = "Ram";                          // string
$age      = 21;                             // integer
$gpa      = 3.75;                           // float
$isActive = true;                           // boolean
$subjects = ["PHP", "DSA", "OS"];           // indexed array
$student  = ["name" => "Ram", "age" => 21]; // associative array
$nothing  = null;                           // NULL

// echo and print
echo "Name: " . $name . "<br>";
echo "Age: $age<br>";
print "GPA: " . $gpa . "<br>";
print "Active: " . ($isActive ? "true" : "false") . "<br><br>";

// print_r() and var_dump() for arrays
echo "<pre>";
print_r($subjects);
print_r($student);
var_dump($subjects);
var_dump($student);
echo "</pre>";

// Checking data types
echo "\$name: " . gettype($name) . "<br>";
echo "\$age: " . gettype($age) . "<br>";
echo "\$gpa: " . gettype($gpa) . "<br>";
echo "\$isActive: " . gettype($isActive) . "<br>";
echo "\$subjects: " . gettype($subjects) . "<br>";
echo "\$nothing: " . gettype($nothing) . "<br>";

var_dump(is_string($name));
var_dump(is_int($age));
var_dump(is_float($gpa));
var_dump(is_bool($isActive));
var_dump(is_array($subjects));
var_dump(is_null($nothing));
?>