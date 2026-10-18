<!-- Write a PHP program to demonstrate static properties and static methods. -->

<?php
class Student{
    public static $schoolName = "ABC School"; // static property

    public static function getSchoolName() { // static method
        return self::$schoolName;
    }

    public static function setSchoolName($name) { // static method to set school name
        self::$schoolName = $name;
    }
}

$s1 = new Student();
Student::setSchoolName("St.Xavier's School");
echo Student::getSchoolName();
echo "<br>";
echo "School Name from object: " . $s1::$schoolName; // accessing static property using object
?>