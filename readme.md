# Sirportly Github Integration

Basic script which utilises Github webhooks to push commit messages to Sirportly.

Put [xx-123] anywhere in the commit message where xx-123 represents the Sirportly ticket reference. If mulitple references are found mulitple posts will be updated.

> $git commit -m "Fixed bug causing incorrect line height [xx-123]"

"Fixed bug causing incorrect line height" will be added to Sirportly as a private post.

## Variables

You can also add variables to the commit message which will be parsed by Sirportly and added to the message...

http://sirportly.com/docs/admin/advanced-features/ticket-variables