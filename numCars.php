<!-- Write a PHP function that accepts the number of people and calculates the minimum
number of cars required when each car can accommodate a maximum of four passengers
and one driver. -->

<?php 
function minCars($numPeople){
    $maxCap = 4;
    $total = $numPeople + 1; // adding 1 for the driver
    $minCars = ceil($total / $maxCap);
    return $minCars;
}

$num = minCars(14);
echo "The minimum number of cars required is : " . $num; // Output: 4
