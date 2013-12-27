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
    <div id="layer-1">
    <div id="layer-2">
    <div id="layer-3" class='content'>

    <?php if(isset($user->username)): ?>
        <div id="left-sidebar">My Games</div>
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

            <?php if(isset($content)) echo $content; ?>
        </div>
        <div id="right-sidebar">Users</div>

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

    <?php if(isset($client_files_body)) echo $client_files_body; ?>

    </div>
    </div>
    </div>
</body>
</html>
