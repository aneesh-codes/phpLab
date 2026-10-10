<!-- function to generate min to sec  -->
 <?php
function minToSec($minutes) {
    $seconds = $minutes * 60;
    return $seconds;
}

echo minToSec(6);