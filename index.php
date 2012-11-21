<?php
include 'shared.php';

$pass = getPassword();

htmlHeaders();

try {

	//Set up the database connection and the connection modes
    $dbh = new PDO("pgsql:host=dbclass.cs.pdx.edu;port=5432;dbname=class28db;user=class28;password=$pass");
    
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
  
    
    //Show a list of all ships
    
    $stmt = $dbh->prepare('SELECT * FROM ships ORDER BY name ASC');
	$stmt->execute();
	
	echo "<div class='pageTitle'>All Ship Histories </div>" ;
	
	echo '<table><tr><th>Ship name</th><th>History</th></tr>';
	foreach ($stmt as $row) {
		// do something with $row
		echo '<tr><td>' . $row['name'] . '</td><td> <a href="viewHistory.php?ship=' . $row['name'] . '">View</a> </td></tr>';
	}
	echo '</table>';	

    $dbh = null;
    

    
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

htmlFooters();
?>