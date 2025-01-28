<?php

// User data
$users = [
    ['Acey', 'Navida', 'acey', 'rajah@123'],
    ['Rafael', 'Tolentino', 'jhonraf', 'rajah@123'],
    ['Vanessa', 'Alcaraz', 'van', 'rajah@123'],
    ['Jamilah', 'Pecayo', 'jam', 'rajah@123'],
    ['Andrei', 'Fernandez', 'andrei', 'rajah@123'],
    ['Lara', 'Magpayo', 'lara', 'rajah@123'],
    ['Geejay', 'Nepomuceno', 'geejay', 'rajah@123'],
    ['Cayla', 'Ramos', 'cayla', 'rajah@123'],
    ['Rima', 'Briones', 'rima', 'rajah@123'],
    ['Ryan', 'Santos', 'ryan', 'rajah@123'],
    ['Des', 'Carandang', 'des', 'rajah@123'],
    ['Camille', 'Gallego', 'mille', 'rajah@123'],
    ['Shin', 'Salon', 'shin', 'rajah@123'],
    ['Joe', 'Abad', 'joe', 'rajah@123'],
    ['Lai', 'Dullon', 'lai', 'rajah@123'],
    ['Regielyn', 'Buenaobra', 'reg', 'rajah@123'],
    ['Melissa', 'Ate', 'mel', 'rajah@123'],
    ['Lea', 'Versoza', 'lea', 'rajah@123'],
    ['Berna', 'Licmo-an', 'berna', 'rajah@123'],
    ['Angelika', 'Sagales', 'angelika', 'rajah@123']
];

// Start building the SQL query
$sql = "INSERT INTO users (first_name, last_name, username, password) VALUES\n";

foreach ($users as $user) {
    [$firstName, $lastName, $username, $password] = $user;
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql .= "('$firstName', '$lastName', '$username', '$hashedPassword'),\n";
}

// Remove trailing comma and add a semicolon
$sql = rtrim($sql, ",\n") . ";";

// Output the SQL query
file_put_contents('insert_users.sql', $sql);
echo "SQL query has been written to insert_users.sql\n";
