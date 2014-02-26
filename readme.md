# Sirportly Github Integration

Basic script which utilises Github webhooks to push commit messages to Sirportly.

Put [spxxx] anywhere in the commit message where xxx represents the Sirportly ticket reference.

> $git commit -m "Fixed bug causing incorrect line height [sp123]"

"Fixed bug causing incorrect line height" will be added to Sirportly as a private post.