<?php

include 'shared.php';

$pass = getPassword();


htmlHeaders();

try {

$ship = $_GET['ship'];


    $dbh = new PDO("pgsql:host=dbclass.cs.pdx.edu;port=5432;dbname=class28db;user=class28;password=$pass");
    
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    

	
	echo "<div class='pageTitle'>History for $ship</div>" ;
	
	echo '<table><tr><th>Event</th><th>Outcome</th><th>Date</th><th>Edit</tr>';
	
	
	//Insert the single row for the Launched date
    $query = "Select TO_CHAR(launched, 'MM/DD/YYYY') as date FROM Ships Where name = :name ";
    $stmt = $dbh->prepare($query);
	$stmt->bindParam(':name', $ship);
	$stmt->execute();
	$row = $stmt->fetch();
	
	echo "<tr><td>Launched</td> <td> </td> <td>" . $row['date'] . "</td></tr>";
	
	
	
	
	//Now get all the rows for the battle dates
	
		  	$query =  
"		Select B.name as Battle, O.result as Outcome, TO_CHAR(B.date, 'MM/DD/YYYY') as Date ".
"		From Battles B INNER JOIN Outcomes O on B.name = O.battle ".
"		where O.ship = :name ".
"		Order by B.Date ASC ";

    
    $stmt = $dbh->prepare($query);
	$stmt->bindParam(':name', $ship);
	$stmt->execute();
	

	foreach ($stmt as $row) {
	//Build each table row with the battle histories
	
			echo '<tr><td>' . $row['battle'] . '</td><td>' . $row['outcome'] . '</td><td> ' 
		. $row['date'] . '</td> <td><a href="editOutcome.php?ship=' . $ship 
		. '&battle=' . $row['battle'] . '" >Edit outcome</a></td></tr>';
		
	}
	echo '</table>';	



	echo '<div class="pageBottomNav">';
	echo "<a href='editOutcome.php?ship=$ship'>Add a new outcome for $ship</a> <br />";
	echo "<a href='index.php'> Back to ships list</a>";
	echo "</div>";

    $dbh = null; 
    
	htmlFooters();
    
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
?>