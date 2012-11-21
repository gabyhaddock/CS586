<?php
include 'shared.php';

$pass = getPassword();


$ship = $_POST['ship'];
$battle = $_POST['battle'] ;
$outcome = $_POST['outcome'] ;
$action = $_POST['action'] ;

try {
    $dbh = new PDO("pgsql:host=dbclass.cs.pdx.edu;port=5432;dbname=class28db;user=class28;password=$pass");
    
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
  //Allow the user to enter a new battle first 
	if ($battle == 'NEW') {
		$battleName = $_POST['battleName'];
		$battleDate = $_POST['battleDate']; 
	
		$query =  "INSERT INTO Battle(name, date) VALUES (:battleName, :battleDate);";
		
		$stmt = $dbh->prepare($query);
		
		$stmt->bindParam(':battleName', $battleName);
		$stmt->bindParam(':battleDate', $battleDate);	
		$stmt->execute();
		
		//Now save the battle name into the regular $battle variable so it can be reused below
		$battle = $battleName;
	}

	
	//Check the $action variable, it is INSERT for a new Outcome and UPDATE for updating an existing
	if ($action == 'INSERT') {
		$query = "INSERT INTO Outcomes (ship, battle, result) "
		. "VALUES (:ship, :battle, :outcome);";
		
	}
	
	else {
	//Update existing row
		$query = "UPDATE Outcomes "
				."SET result = :outcome "
				."WHERE ship = :ship AND battle = :battle;";
	}
	
				
	$stmt = $dbh->prepare($query);
			
	$stmt->bindParam(':ship', $ship);
	$stmt->bindParam(':battle', $battle);
	$stmt->bindParam(':outcome', $outcome);
	$stmt->execute();	
	
	//Redirect back to the ship's history page
	  header( "Location: http://web.cecs.pdx.edu/~ghaddock/CS586/viewHistory.php?ship=" .$ship ) ;
	

} catch (PDOException $e) {
    echo "Error!: " . $e->getMessage() . "<br/>";
    die();
} 

?>