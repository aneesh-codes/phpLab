<?php
namespace School {
    class Student {
        public function getDetails() {
            return "This is a student from the School namespace.";
        }
    }
}

namespace College {
    class Student {
        public function getDetails() {
            return "This is a student from the College namespace.";
        }
    }
}

namespace {
    // Accessing classes from different namespaces
    $schoolStudent = new \School\Student();
    $collegeStudent = new \College\Student();

    echo $schoolStudent->getDetails();
    echo "<br>";
    echo $collegeStudent->getDetails();
}