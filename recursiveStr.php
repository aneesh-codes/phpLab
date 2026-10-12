<!-- Write a recursive PHP function that accepts a string and returns its length. -->

<?php
function recStrLen($str){
    if($str == ""){
        return 0;
    } else {
        return 1 + recStrLen(substr($str, 1));
    }
}

$sen = recStrLen('We are learning PHP'); // Output: 19
echo "The length of the string is : " . $sen;
