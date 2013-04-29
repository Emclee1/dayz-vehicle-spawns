<?php
/* 3D Editor Mission File Parser
 * For PWNOZOR'S PRIVATE SERVER PACK
 *
 * This will take your mission.sqm file and add all spawn points to your object_spawns table
 * Written by: Planek
 * Edited by : MvK (04.29.2013) - Pwnozor's server pack compatibility
 *
 * You can go through and edit
 * Edit this info to match your database config//
 *
 */
 
// DATABASE CONNECTION INFO //
$dbhost = 'localhost'; // e.g. localhost
$dbname = 'hivemind'; // e.g. dayz_1.Chernarus
$dbuser = 'dayz'; // e.g. dayz
$dbpass = 'dayz'; // e.g. dayz

$fileName = 'mission.sqf'; //path to your mission.sqm file
///////////////////

// DO NOT EDIT ANYTHING BELOW THIS LINE //
/*--------------------------------------*/

$conn = mysql_connect($dbhost, $dbuser, $dbpass);
mysql_select_db($dbname, $conn);
$missionfile = file_get_contents($fileName);
$rows = explode("\n",$missionfile);
array_shift($rows);
$vehiclecount =0;
?>

<table border=1px>
<tr><th>Class Name</th><th>Position</th></tr>

<?php
	
		$IDQuery = "SELECT ObjectUID
		FROM object_spawns;";
		$resultIDQuery = mysql_query($IDQuery);
		while ($row = mysql_fetch_array($resultIDQuery, MYSQL_NUM)) 
			$userDataUIDs[] = $row[0];
			$userDataMAPID[] = $row[5];
		$objectuid = max($userDataUIDs)+1;
		$mapid = max($userDataUIDs)+1;
		
for($i=0;$i<count($rows);$i++)
{
	$direction = rand(0,359);
	$n=0;
	if (strpos($rows[$i],'_this = createVehicle [') !== false)
	{
		$strings = explode("\"",$rows[$i]);
		$firstOpenBracket = strpos($rows[$i], "[");
		$secondOpenBracket = strpos($rows[$i], "[", $firstOpenBracket + strlen("]"));
		$firstCloseBracket = strpos($rows[$i], "]");
		
		$pos = "[$direction," . substr($rows[$i],$secondOpenBracket, $firstCloseBracket-$secondOpenBracket+1) . "]";
		$pos = str_replace(array(' '), '',$pos);
		$newPos = explode(",",$pos);
		$pos = "[$direction," . $newPos[1] . "," . $newPos[2] . ",0]]";
		$pos = str_replace(array(' '), '',$pos);
		$pos = str_replace(array(']],0'), ',0',$pos);
		
		//Class Check
		$checkClassNameQuery = "SELECT *
		FROM object_classes;";
		$resultClassNameQuery = mysql_query($checkClassNameQuery);
		$userDataClassNameQuery;
		$userDataVehicleIDs;
		while ($row = mysql_fetch_array($resultClassNameQuery, MYSQL_ASSOC)) 
			$userDataClassNameQuery[] = $row['Classname'];

		$matchFound = 0;
		for($j=0;$j<count($userDataClassNameQuery)-1;$j++)
		{
			if ($strings[1] == $userDataClassNameQuery[$j]) 
			{
				$matchFound = 1;
				$n++;
			} ;
		}
		//Class Check End
		
		$insertQuery = "INSERT INTO `object_spawns` (`ObjectUID`, `Classname`, `Worldspace`, `Inventory`, `Hitpoints`, `MapID`, `Last_changed`) 
		VALUES ('$objectuid', '$strings[1]', '$pos', '[]', '[]', '$mapid', 'NULL');";
		$resultInsertQuery = mysql_query($insertQuery);
		$objectuid++;
		$mapid++;
		$vehiclecount++;
		?><tr><td><?php echo $strings[1]?></td><td><?php echo $pos?></td></tr><?php
	}
		
}
?>
</table><br><br><b>
<?php
echo $vehiclecount;
?> </b>vehicles added to the database spawn tables.<br>
