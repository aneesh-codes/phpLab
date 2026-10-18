<!-- Write a PHP program to demonstrate constructors and destructors using a suitable real-
world class. -->

<?php
class Student {
    public $name;
    public $marks;

    // constructor
    public function __construct($name, $marks) {
        $this->name = $name;
        $this->marks = $marks;
        echo "Constructor called for student: $this->name with marks: $this->marks<br>";
    }

    // destructor
    public function __destruct() {
        echo "Destructor called for student: $this->name<br>";
    }
}

$student = new Student("Ram", 85);
echo "Student Name: " . $student->name . "<br>";
echo "Student Marks: " . $student->marks . "<br>";
?>