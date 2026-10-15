<!-- PHP script to create an associative array containing student/person information
such as name, address, email, phone, and website, and display the array contents in an
HTML table. -->

<?php
$person = [
    "name" => "John Doe",
    "address" => "123 Main St",
    "email" => "john.doe@example.com",
    "phone" => "555-1234",
    "website" => "https://www.johndoe.com"
];

echo "<table border='1'>";
echo "<tr><th>Field</th><th>Value</th></tr>";
foreach ($person as $field => $value) {
    echo "<tr><td>$field</td><td>$value</td></tr>";
}
echo "</table>";