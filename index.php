<?php
// Load the config and the system object
require_once('config.php');
require_once('lib'.DS.'System.php');

// Define the autoload stuffs
function __autoload($class){
    include "lib/$class.php";
}

$_POST['payload'] = '

{
  "ref": "refs/heads/master",
  "after": "0123b6f74be0dc7636d08901b1ddc2b6b0f455b7",
  "before": "75e77f856b746fa847ea129d231874e34cc10914",
  "created": false,
  "deleted": false,
  "forced": false,
  "compare": "https://github.com/jadamsfolk/Github-Sirportly-Integration/compare/75e77f856b74...0123b6f74be0",
  "commits": [
    {
      "id": "0123b6f74be0dc7636d08901b1ddc2b6b0f455b7",
      "distinct": true,
      "message": "[DZ-583066] Slight alteration to readme",
      "timestamp": "2014-02-26T08:46:52-08:00",
      "url": "https://github.com/jadamsfolk/Github-Sirportly-Integration/commit/0123b6f74be0dc7636d08901b1ddc2b6b0f455b7",
      "author": {
        "name": "Jon Adams",
        "email": "jon.adams@wearefolk.com",
        "username": "jadamsfolk"
      },
      "committer": {
        "name": "Jon Adams",
        "email": "jon.adams@wearefolk.com",
        "username": "jadamsfolk"
      },
      "added": [

      ],
      "removed": [

      ],
      "modified": [
        "readme.md"
      ]
    }
  ],
  "head_commit": {
    "id": "0123b6f74be0dc7636d08901b1ddc2b6b0f455b7",
    "distinct": true,
    "message": "[DZ-583066] Slight alteration to readme",
    "timestamp": "2014-02-26T08:46:52-08:00",
    "url": "https://github.com/jadamsfolk/Github-Sirportly-Integration/commit/0123b6f74be0dc7636d08901b1ddc2b6b0f455b7",
    "author": {
      "name": "Jon Adams",
      "email": "jon.adams@wearefolk.com",
      "username": "jadamsfolk"
    },
    "committer": {
      "name": "Jon Adams",
      "email": "jon.adams@wearefolk.com",
      "username": "jadamsfolk"
    },
    "added": [

    ],
    "removed": [

    ],
    "modified": [
      "readme.md"
    ]
  },
  "repository": {
    "id": 17205895,
    "name": "Github-Sirportly-Integration",
    "url": "https://github.com/jadamsfolk/Github-Sirportly-Integration",
    "description": "Utilises Github webhook and Sirportly API to pop commit messages into Sirportly as comments.",
    "watchers": 0,
    "stargazers": 0,
    "forks": 0,
    "fork": false,
    "size": 0,
    "owner": {
      "name": "jadamsfolk",
      "email": "jon.adams@wearefolk.com"
    },
    "private": false,
    "open_issues": 0,
    "has_issues": true,
    "has_downloads": true,
    "has_wiki": true,
    "language": "PHP",
    "created_at": 1393407572,
    "pushed_at": 1393433223,
    "master_branch": "master"
  },
  "pusher": {
    "name": "jadamsfolk",
    "email": "jon.adams@wearefolk.com"
  }
}

';

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

                // If matches exist, loop through, pull the ticket number out, check if a ticket exists, post to sirportly
                if(!empty($matches[0]) && is_array($matches[0])){
                    foreach($matches[0] as $ticket_reference){
                        $ticket_reference = str_replace(array('[', ']'), '', $ticket_reference);

                        // Check that the ticket exists in sirportly
                        if($sirportly_api->getTicket($ticket_reference)){
                            $name = $commit->author->name;
                            $email = $commit->author->email;
                            $ticket_reference = $ticket_reference;
                            $message = trim(preg_replace('/\[[^)]*\]|[\[\]]/', '', $commit->message));

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