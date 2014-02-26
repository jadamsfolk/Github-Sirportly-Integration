<?php
// Load the config and the system object
require_once('config.php');
require_once('lib'.DS.'System.php');

// Define the autoload stuffs
spl_autoload_extensions('.php');
spl_autoload_register(array('System', 'autoLoad'));

// Instantiate system and check config options
$sys = new System();

// Check for errors in the config
if(!$msg = $sys->configErrorCheck()){

    // Gather the data and pass it in to the Github obj for processing


    // Load and pass the data to Sirportly


    // Report success/failure (failures added to apperror.log)

    die('Success!');

} else {
    die($msg);
}