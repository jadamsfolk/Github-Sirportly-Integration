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
    // TODO: Gather the Github data here

    ob_start();
    var_dump($_GET);
    echo '-------------------------------------';
    var_dump($_POST);
    $contents = ob_get_contents();
    ob_end_clean();
    error_log($contents, 3, ROOT_DIR.'logs'.DS.'github.log');

    die;

    $name = '';
    $email = '';
    $ticket_number = '';
    $message = '';

    // Load Sirportly and pass the Github data in
    $sirportly_api = new SirportlyAPI(SIRPORTLY_TOKEN, SIRPORTLY_SECRET);

    // Perform the post
    if($sirportly_api->postToTicket($name, $email, $ticket_number, $message)){
        $msg = 'success!';
    } else {
        $msg = 'failed';
    }
}

die($msg);