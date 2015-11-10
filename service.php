<?php

function createTable() {

    $db = new SQLite3('data.db');
    if (!$db) die ();
	
	// Create the table 
	$command = "CREATE TABLE Queue(
	id INTEGER PRIMARY KEY,
	URL TEXT NOT NULL
	)";

	$ok = $db->exec($command);

	if (!$ok) die("Cannot create database. </br></br>");
	else echo "Database created successfully</br></br>";
	
	$db->close();	
}

function tableExists() {

    $db = new SQLite3('data.db');
    if (!$db) die ();

	$check = "SELECT name FROM sqlite_master WHERE type='table' AND name='Queue'";
	$response = $db->query($check);
	$contents = $response->fetchArray(SQLITE_ASSOC);

	return $contents;
}

function requestPrintURL() {

    $db = new SQLite3('data.db');
    if (!$db) die ();
	
	$query = "SELECT * FROM Queue ORDER BY ROWID ASC LIMIT 1";
	$result = $db->query($query);
    $row = $result->fetchArray(SQLITE_ASSOC);

	// If the table contains a row
	if ($row) {
		echo($row['URL']);
		$query = "DELETE FROM Queue WHERE id=".$row['id'];
		$result = $db->query($query);	
	}

/* 	else echo("Nothing to print."); */
	
	$db->close();
}

function printDatabase() {

    $db = new SQLite3('data.db');
    if (!$db) die ();

	//Do the query
	$query = "SELECT * FROM Queue";
	$result = $db->query($query);
	//iterate over all the rows
	while($row = $result->fetchArray(SQLITE_ASSOC)){
    	//iterate over all the fields
    	foreach($row as $key => $val){
        	//generate output
        	echo $key . ": " . $val . "<BR />";
        }
    }

    $db->close();
}

function addPrintURL($printURL) {

    $db = new SQLite3('data.db');
    if (!$db) die ();
    
	$command = "INSERT INTO Queue VALUES(NULL,'$printURL')";

	$response = $db->exec($command);
	if (!$response) die("Cannot add print to queue.");
	else echo "Print added to queue.";

	$db->close();
}

function usage() {
	
	echo("Usage:</br></br>Request print URL = ?requestPrintURL</br>List items in database = ?printDatabase</br>Add URL of a photo to print = ?addPrintURL&url=<i>urlOfPhoto</i>"); 
}


if (!tableExists()) createTable();

if(isset($_GET['requestPrintURL'])) die(requestPrintURL());
if(isset($_GET['printDatabase'])) die(printDatabase());
if(isset($_GET['addPrintURL']) && isset($_GET['url'])) die(addPrintURL($_GET['url']));
else usage();

?>
