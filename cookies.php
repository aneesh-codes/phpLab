<!-- Write a PHP program to create, access, and destroy a cookie. Display appropriate
messages for each operation. -->

<?php
// Create a cookie
$cookie_name = "user";
$cookie_value = "John Doe";
setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
echo "Cookie named '" .$cookie_name. "' is created with value '". 
$cookie_value. "'<br>";

// access the cookie
if(isset($_COOKIE[$cookie_name])){

    echo "Cookie '" .$cookie_name. "' is accessed with value:". 
    $_COOKIE[$cookie_name]. "<br>";

}
// destroy the cookie
setcookie($cookie_name, "", time() - 3600, "/"); // set the expiration time to the past
echo "Cookie named '" .$cookie_name. "' is destroyed.<br>";
