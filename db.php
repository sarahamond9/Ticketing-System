<!--Sarah Amond-->
<!--C00308719-->
<?php
$host = 'localhost'; //it is on the local host
$db   = 'ticketing_system';// the database name
$user = 'root';// user of the database
$pass = ''; // MySQL Password
$charset = 'utf8mb4'; // Define the character set for the database connection

$dsn = "mysql:host=$host;dbname=$db;charset=$charset"; // Construct the Data Source Name with host, db name, and charset
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Set error mode to throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,     // Set default fetch mode to associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                // Disable emulation for true prepared statements
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options); // Attempt to create a new PDO database instance
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode()); // Catch and rethrow any connection exceptions
}
?>
