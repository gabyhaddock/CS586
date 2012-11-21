<?php

include 'shared.php';




htmlHeaders();

try {

	$pass = getPassword();


	$ship = $_GET['ship'];
	
	$battle = ""; 
	
	
	if (isset($_GET['battle'])) {
		$battle = $_GET['battle']; 
	}

	$dbh = new PDO("pgsql:host=dbclass.cs.pdx.edu;port=5432;dbname=class28db;user=class28;password=$pass");
  
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//Use the same form for updates and inserts.

    
  	echo "<form method='POST' action='saveOutcome.php'>";

//See if the ship + battle name already exists -- if so, this is an update
	$stmt = $dbh->prepare("Select count(*) FROM outcomes WHERE ship = :ship AND battle = :battle");
	$stmt->bindParam(':ship', $ship);
	$stmt->bindParam(':battle', $battle);
	$stmt->execute();
	
	echo "<input type='hidden' name='ship' value=$ship />";
	
	if ($stmt->fetchColumn() == 1) {
		//Edit an existing outcome that matches the ship + battle that were passed in
		
		echo "<input type='hidden' name='action' value='UPDATE' />";
		echo "<input type='hidden' name='battle' value='$battle' />";
		
		echo "<div class='pageTitle'>Edit Outcome for $ship in $battle </div>" ;
		echo "<table>";
		echo "<tr><th>Ship</th> <td>$ship</td> </tr>";
		echo "<tr><th>Battle</th> <td>$battle</td> </tr>";

//List the outcome for this ship + battle
		$outcomeQuery = "Select result FROM outcomes WHERE ship = :ship AND battle = :battle";
		$stmt = $dbh->prepare($outcomeQuery);
	
		$stmt->bindParam(':ship', $ship);
		$stmt->bindParam(':battle', $battle);
		
		$stmt->execute();
		

		$outcome = $stmt->fetchColumn();
		echo "<tr><th>Outcome</th> ";
		echo "<td><input type='text' name='outcome' value='$outcome'  /> </td></tr>";
		
		echo "</table>";
		
	}
	else {
		echo "<input type='hidden' name='action' value='INSERT' />";
	
//Create a new outcome for the ship, user can choose a battle name and type in the outcome
		echo "<div class='pageTitle'>New Outcome for $ship</div>";
	
		
		echo "<table style='width: 800px;' >";
		echo "<tr><th>Ship</th> <td>$ship</td> </tr>";
		echo "<tr><th>Battle</th> <td>";
		
		
		
//Find battles that could be paired with this ship, that do not cause a PK violation in Outcomes 	
		$battlesQuery =  
			"Select name, TO_CHAR(date, 'MM/DD/YYYY') as date from Battles ". 
			"Where name not in ( ". 
				"Select battle from Outcomes ". 
				"where ship = :ship " .
			") ";
			
		$stmt = $dbh->prepare($battlesQuery);
	
		$stmt->bindParam(':ship', $ship);
		$stmt->execute();
		
		foreach ($stmt as $row) {
		//Build each radio button
			$rowName = $row['name'];
			$rowDate = $row['date'];
			
			echo "<input type='radio' name='battle' value='$rowName' id='$rowName' /> " ;

			echo "<label for='$rowName'>$rowName -- $rowDate</label> <br />";	
		}
		
		//Show a radio button for "new battle"
		echo "<input type='radio' name='battle' value='NEW' id='NEW'>";
		echo "<label for='NEW'> New battle name:";
		echo "<input type='text' name='battleName' />";
		echo "Date (MM/DD/YYYY): " ;
		echo "<input type='text' name='battleDate'/>";
		echo "</label>";
		
		
		echo "</td></tr>";
		echo "<tr><th>Outcome</th> <td><input type='text' name='outcome' /> </td> </tr>";
		echo "</table>";

		
	}
	
	echo "<input type='submit' value='Save' />";
	echo "</form>";
	
	echo '<div class="pageBottomNav">';
	echo "<a href='editOutcome.php?ship=$ship'>Add a new outcome for $ship</a> <br />";
	echo "<a href='viewHistory.php?ship=$ship'>Back to history for $ship</a> <br />";
	echo "<a href='index.php'> Back to ships list</a>";
	echo "</div>";

    $dbh = null; 
    
    
    htmlFooters();
    
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
} catch (Exception $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}


?>