p4.plesiosaur.us
================

SUMMARY
This is a simple website where users can sign up for an account and login
and logout of their account and play Jotto once they have an account.
Jotto is a word game that Susan introduced in class so I don’t think I
need to explain it, but there are fairly detailed instructions for how to
play on the gameplay page if a refresher is required.

FEATURES
- signup for an account
- - basic error checking on user input from signup form
- account login
- - basic error checking on user input from login form
- account logout
- “restricted access” view (routes to index page with message to login)
- 3-pane view that displays current user games as well as a list of all users
- ability to view user profiles (containing very basic gameplay statistics)
- ability to start new games
- ability to leave a game at any point and resume it later from that point
- GAMEPLAY FEATURES:
- - view list of previous guesses and computer responses
- - alphabet with color-changing letters to help user mark when they have
	determined a letter to be in or out of the secret word
- - reset alphabet to default colors if user made an error
- - error checking on user guess input

JAVASCRIPT
All gameplay is handled via Javascript and AJAX calls. The entire game
play area is just a single page that’s being manipulated with jQuery.
There isn’t much integration between the Javascript and the PHP, I suppose,
but I felt this was cleaner and made more sense anyway.

ADDITIONAL COMMENTS
Some aspects of the game are difficult to explain without a wall of text,
and I apologize for that; hopefully the layout is intuitive enough.

Originally, I had planned to make it a more social website where users
could initiate games with other users, but that turned out to be more
difficult than I could handle in the time I had (which was much shorter
than it could have been, and I’m really sorry this is so late). But that
explains why there are occasionally patches of code that aren't currently
being used.

Lastly, I tried my best to put together a representative word list of
reasonably common but not mundane 5-letter words, but I've no idea how
good of a job I did, really. On the other hand, I learned a bunch of
useful Unix text file manipulation commands....

