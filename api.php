<?php
//This file is controlling the API request on the backend
//please note: we have to use a places.json file to store the data as sessions have problems in LXC

//include necessary files
include $_SERVER['DOCUMENT_ROOT'] . "/assets/functions/overtake.php";
include $_SERVER['DOCUMENT_ROOT'] . "/assets/functions/pole.php";

//file to store driverPlaces
$placesFile = "places.json";

//create a method controller for the API
$cAPI = 0;
//check the slugs in URL
$slugs = explode("/", $_SERVER['REQUEST_URI']);
//limiting the number of parameters
if (count ($slugs) > 6) {
	echo '[{"error": "too many parameters"}]';
	exit ();
}
if ($slugs[1] == "api" && $slugs[2] == "drivers" && !isset($slugs[3])) {
	//it's a GET of the drivers (1)
	$cAPI = 1;
}
if (isset($slugs[4])) {
	if ($slugs[1] == "api" && $slugs[2] == "drivers" && $slugs[4] == "overtake") {
		//it's an OVERTAKE (2)
		$cAPI = 2;
	}
	if ($slugs[1] == "api" && $slugs[2] == "drivers" && $slugs[4] == "multiovertake") {
		//it's a MULTIOVERTAKE (3)
		$cAPI = 3;
		$enemyId = intval (sprintf ("%d", $slugs[5]));

	}
	//take the id (int) of the overtaking driver (using safe method)
	$heroId = intval (sprintf ("%d", $slugs[3]));
}
if ($slugs[1] == "api" && $slugs[2] == "reset") {
	//removing content from placesFile, the race can be restarted
	file_put_contents($placesFile, "");
	exit ();
}

//slugs array is not needed anymore
unset ($slugs);

//json file path
$driversFile = 'assets/json/drivers.json';

//checking if drivers.json is existing or not
if (file_exists($driversFile)) {
	//load drivers.json file content to a string
	$driversJSON = file_get_contents($driversFile);
} else {
	//if drivers.json does not exist, then echo an error message and exit application
	echo '[{"error": "drivers.json file not found"}]';
	exit ();
}
//create an empty array
$drivers = [];
//converting json to array
$drivers = json_decode ($driversJSON, true);
//deleting unnecessary vars
unset ($driversJSON, $driversFile);
//get number of drivers
$numberOfDrivers = count ($drivers);

//add imgUrl to the drivers array
for ($i=0; $i<$numberOfDrivers; $i++) {
	//code would be easier but we take imgUrl from lastname now for fun
	//strtolower converts all chars in string to lower characters and substr (string,0,3) takes the first 3 chars from a string
	//if we would used "code" then it was: $drivers[$i]['imgUrl'] = "/static/" . strtolower ($drivers[$i]['code']) . ".png";
	$drivers[$i]['imgUrl'] = "/static/" . strtolower (substr ($drivers[$i]['lastname'], 0, 3)) . ".png";
}


//counter for foreach (auto-increase)
$x = 0;

//checking if placesFile has any data
if (filesize($placesFile) == 0) {

	//create empty array
	$driverPlace = [];

	//create $ph object
	$ph = new PoleHandling ();
	//randomize places
	$poles = $ph -> getRandomPoles ();
	//delete $ph object
	unset ($ph);
} else {
	//load data from json file and convert it to array
	$poles = json_decode (file_get_contents($placesFile), true);
}
	
foreach ($poles as $pole) {
	//adding polePositions to drivers array as place
	$drivers[$x]['place'] = $pole;
	//store it in separate array
	$driverPlace[$x] = $pole;
	//auto-increase $x
	$x++;
}

if (filesize($placesFile) == 0) {
	//convert array to json and write it to a temporary file
	file_put_contents($placesFile, json_encode($driverPlace));
}
	
//handling overtake
if ($cAPI > 1) {
	//create $ot object
	$ot = new Overtake ();
	//hero is our choosen one, enemy is the one to overtake
	$driverPlace = $ot -> overtake ($driverPlace, $heroId);
	unset ($ot);
	//update the places.json file
	file_put_contents($placesFile, json_encode(array_values($driverPlace)));
	unset ($driverPlace);
}

unset ($placesFile);

if ($cAPI === 1) {
	//return pretty JSON with driver details
	//echo "<pre>" . stripslashes (json_encode($drivers, JSON_PRETTY_PRINT)) . "</pre>";
	echo stripslashes (json_encode($drivers, JSON_PRETTY_PRINT));
}

exit ();
?>
