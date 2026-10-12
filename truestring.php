<!-- Write a PHP function that accepts two strings and returns true if both strings contain the
same number of characters; otherwise, return false. -->

<?php
function SameLength($str1, $str2){
    if(recStrLen($str1)==recStrLen($str2)){
        return true;
    } else {
        return false;
    }
}

$str = SameLength("hello", "world");
echo "The output is :" . $str; // Output: 1 (true)