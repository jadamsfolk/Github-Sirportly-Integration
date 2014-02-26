# Sirportly Github Integration

Basic script which utilises Github webhooks to push commit messages to Sirportly.

Put [xx-123] anywhere in the commit message where xx-123 represents the Sirportly ticket reference. If mulitple references are found mulitple posts will be updated.

> $git commit -m "Fixed bug causing incorrect line height [xx-123]"

"Fixed bug causing incorrect line height" will be added to Sirportly as a private post.

## Setup

1) Host the script somewhere online.
2) Copy config-example.php to config.php
3) In Sirportly add a new API Token (https://your-company.sirportly.com/admin/api_tokens)
3) In the new config.php file add your Sirportly API Token and Secret

> define('SIRPORTLY_TOKEN', 'xxxxx-xxxxx-xxxxx-xxxxx-xxxxx');
> define('SIRPORTLY_SECRET', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxx');

4) Go to the settings in your Github repo
5) Go to Webhooks & Services
6) Add a new webhook. Add the URL for your hosted script. Set the payload version to 'application/vnd.github.v3+form'. Select 'Just the push event.' Select Active.
7) Done. Now if you commit using an email address recognised by your Sirportly and a valid ticket reference then the commit message will be automatically added to Sirportly.

## Variables

You can also add variables to the commit message which will be parsed by Sirportly and added to the message...

http://sirportly.com/docs/admin/advanced-features/ticket-variables