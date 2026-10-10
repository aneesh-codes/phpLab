<!-- function to write person years to day -->

<?php
function yearsToDays($years){
    $days = $years * 365;
    return $days;
}

// function call
echo yearsToDays(20);