<?php
/* The purpose of this file to establish the connection between your php page
 * and the database. On successfull completion you will have a variable $db 
 * that is your database connection ready to use.
 */

//include("/usr/local/uvm-inc/jdedward.inc");

$debug = false;

$databaseName="JDEDWARD_Maxine_Davis_Art"; 
$dbUsername="jdedward_writer";
$dbUserPass="dKRvdxTd9bG9";

$dsn = 'mysql:host=webdb.uvm.edu;dbname=';

function dbConnect($dbName){
    global $db, $dsn, $dbUsername, $dbUserPass;

    if (!$db) $db = new PDO($dsn . $dbName, $dbUsername, $dbUserPass); 
        if (!$db) {
          echo '<p>A You are NOT connected to the database.</p>';
          return 0;
        } else {
             //echo '<p>A You are connected to the database.</p>';
          return $db;
        }
} 

// create the PDO object
try {     
    $db=dbConnect($databaseName);
    //if($debug) echo '<p>A You are connected to the database!</p>';
} catch (PDOException $e) {
    $error_message = $e->getMessage();
    //if($debug) echo "<p>An error occurred while connecting to the database: $error_message </p>";
}
?>