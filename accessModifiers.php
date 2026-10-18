<!-- PHP program to demonstrate public, private, and protected properties and methods. -->
<?php
class Student {
    public $name;        // accessible everywhere
    private $marks;      // accessible only inside Student
    protected $age;      // accessible in Student and child classes

    public function setMarks($marks) {
        $this->marks = $marks;
    }

    private function getMarks() {
        return $this->marks;
    }

    protected function setAge($age) {
        $this->age = $age;
    }

    // public wrapper to reach the private method
    public function showMarks() {
        return $this->getMarks();
    }
}

class GradStudent extends Student {
    public function updateAge($age) {
        $this->setAge($age);   // protected method works in a child class
        return $this->age;
    }
}

$s = new Student();
$s->name = "Ram";
$s->setMarks(85);
echo "Name: " . $s->name . "<br>";
echo "Marks: " . $s->showMarks() . "<br>";

// These would cause fatal errors if uncommented:
// echo $s->marks;      // private property
// $s->getMarks();      // private method
// $s->setAge(20);      // protected method

$g = new GradStudent();
echo "Age: " . $g->updateAge(22);
?>