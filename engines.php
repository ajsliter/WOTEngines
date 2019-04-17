<?php

session_start();
if(!isset($_SESSION["tJHSQRuoNnWUwLRe"])){ // if "user" not set,
	session_destroy();
	header('Location: login.php');     // go to login page
	exit();
}

// include the class that handles database connections
require "database.php";

// include the class containing functions/methods for "Engine" table
// Note: this application uses "Engines" table, not "cusotmers" table
require "engines.class.php";
$egne = new Engine();
 
// set active record field values, if any 
// (field values not set for display_list and display_create_form)
if(isset($_GET["id"]))          		$egne->id = htmlspecialchars($_GET["id"]); 
if(isset($_POST["Manufacturer"]))       $egne->Manufacturer = htmlspecialchars($_POST["Manufacturer"]);
if(isset($_POST["Model"]))      		$egne->Model = htmlspecialchars($_POST["Model"]);
if(isset($_POST["CountryOfOrigin"]))    $egne->CountryOfOrigin = htmlspecialchars($_POST["CountryOfOrigin"]);
if(isset($_POST["FuelType"]))     		$egne->FuelType = htmlspecialchars($_POST["FuelType"]);
if(isset($_POST["Horsepower"]))     	$egne->Horsepower = htmlspecialchars($_POST["Horsepower"]);

// "fun" is short for "function" to be invoked 
if(isset($_GET["fun"])) $fun = $_GET["fun"];
else $fun = "display_list"; 

switch ($fun) {
    case "display_list":        $egne->list_records();
        break;
    case "display_create_form": $egne->create_record(); 
        break;
    case "display_read_form":   $egne->read_record($egne->id); 
        break;
    case "display_update_form": $egne->update_record($egne->id);
        break;
    case "display_delete_form": $egne->delete_record($egne->id); 
        break;
    case "insert_db_record":    $egne->insert_db_record(); 
        break;
    case "update_db_record":    $egne->update_db_record($egne->id);
        break;
    case "delete_db_record":    $egne->delete_db_record($egne->id);
        break;
    default: 
        echo "Error: Invalid function call (engines.php)";
        exit();
        break;
}

