hereeee
<?php
$servername = "localhost";
$username = "versieatsun";
$password = "w4Fg&f33";
$dbname = "versieatsdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
   // exit('done');
	die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully";

$sql = "SELECT *  FROM 	mt_admin_user";
$result = $conn->query($sql);
echo "<pre>"; print_r($result->fetch_assoc()); exit;  

?>
