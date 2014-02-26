<?php
// Load the config and the system object
require_once('config.php');
require_once('lib'.DS.'System.php');

// Define the autoload stuffs
function __autoload($class){
    include "lib/$class.php";
}

// Instantiate system and check config options
$sys = new System();

// Check for errors in the config
if(!$sys->configErrorCheck()){
    if(!empty($_POST['payload'])){
        $payload = json_decode($_POST['payload']);

        // Ensure that the payload contains commits
        if(!empty($payload->commits)){
            // Load Sirportly and pass the Github data in
            $sirportly_api = new SirportlyAPI(SIRPORTLY_TOKEN, SIRPORTLY_SECRET, true);

            foreach($payload->commits as $commit){
                // Check for square square brackets in the message, pop the result into $matches
                if($commit->message){
                    preg_match_all("/\[.*?\]/", $commit->message, $matches);
                }

                // If matches exist, loop through, pull the ticket number out, check if a ticket exists, post to Sirportly
                if(!empty($matches[0]) && is_array($matches[0])){
                    foreach($matches[0] as $ticket_reference){
                        $ticket_reference = str_replace(array('[', ']'), '', $ticket_reference);

                        // Check that the ticket exists in sirportly
                        if($sirportly_api->getTicket($ticket_reference)){
                            $name = $commit->author->name;
                            $email = $commit->author->email;
                            $ticket_reference = $ticket_reference;
                            $message = 'Commit message'.PHP_EOL.trim(preg_replace('/\[[^)]*\]|[\[\]]/', '', $commit->message)).PHP_EOL.PHP_EOL.$commit->url;

                            // Perform the post
                            if($sirportly_api->postToTicket($name, $email, $ticket_reference, $message)){
                                $msgs[] = 'Sirportly API post success!';
                            } else {
                                $msgs[] = 'Sirportly API post failed';
                            }
                        }
                    }
                } else {
                    $msgs[] = 'No ticket number matches';
                }
            }
        } else {
            $msgs[] = 'No commit data found';
        }
    } else {
        $msgs[] = 'Empty payload';
    }
} else {
    $msgs[] = "Config error";
}

if(!empty($msgs) && is_array($msgs)){
    foreach($msgs as $msg){
        echo $msg;
    }
}