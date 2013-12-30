<!DOCTYPE html>
<html>
<head>
    <title><?php if(isset($title)) echo $title; ?></title>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />	
    <link rel='stylesheet' href='/css/main.css'>

    <!-- Controller Specific JS/CSS -->
    <?php if(isset($client_files_head)) echo $client_files_head; ?>
	
</head>

<body>	
    <div id='wrapper'>

<!-- LOGGED-IN USER SEES LIST OF THEIR GAMES AND OTHER USERS, AND APPROPRIATE LINKS -->
<!-- THE SIDEBARS WILL BE CHANGED VIA JAVASCRIPT LATER ON DURING GAMEPLAY -->
    <?php if(isset($user->username)): ?>
        <div id="left-sidebar">
            <h2>My Games</h2>
            <?php if(isset($games)): ?>
                <?php foreach($games as $game): ?>
                    <a href="/game/play/<?=$game['game_id']?>">Game #<?=$game['game_id']?></a><br>
                    <?php if($game['last_move'] == NULL): ?>
                        Started <?=$game['last_move_date']?><br>
                        No moves yet.
                    <?php else: ?>
                        Last guess: <?=$game['last_move']?><br>
                        at <?=$game['last_move_date']?>
                    <?php endif; ?>
                    <br><br>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div id="middle-column">
            <ul id="navigation">
                <li><a href="/">home</a></li>
                <li><a href="/user/profile">my stats</a></li>
                <li><a href="/user/logout">log out</a></li>
            </ul>

            <h1><?=APP_NAME?>!</h1>

            <?php if(isset($message)): ?>
                <div class='user-message'>
                    <?=$message?>
                </div>
            <?php endif; ?>

            <div id="content">
                <?php if(isset($content)) echo $content; ?>
            </div>

        </div>
        <div id="right-sidebar">
            <h2>Users</h2>
            <?php if(isset($user_list)): ?>
                #<br>
                <?php foreach($user_list as $user_x): ?>
                    <a href="/user/profile/<?=$user_x?>"><?=$user_x?></a><br>
                    #<br>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

<!-- NON-LOGGED IN USER SEES A LANDING PAGE FOR LOGGING IN -->
    <?php else: ?>

        <ul id="navigation">
            <li><a href="/">home</a></li>
            <li><a href="/user/signup">sign up</a></li>
        </ul>

        <h1><?=APP_NAME?>!</h1>        

        <?php if(isset($message)): ?>
            <div class='user-message'>
                <?=$message?>
            </div>
        <?php endif; ?>

        <?php if(isset($content)) echo $content; ?>

    <?php endif; ?>

    </div>

<!-- JAVASCRIPT MAGIC -->
    <?php if(isset($client_files_body)) echo $client_files_body; ?>

</body>
</html>
