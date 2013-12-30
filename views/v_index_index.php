<!-- HOME PAGE -->

<!-- VARIOUS ERROR/CONFIRMATION MESSAGES GO HERE -->
<?php if(isset($message)): ?>
    <div class="user-message">
        <?=$message?>
    </div>
<?php endif; ?>

<!-- LOGGED-IN USER SEES A WELCOME MESSAGE AND LIST OF OPTIONS INCLUDING NEWGAME -->
<?php if($user): ?>

    <h2>Hello, <?=$user->username?>!</h2>

    From here, you can:

    <ul>
        <li><a href="/game/newgame">Start a new game.</a></li>
        <li>Select an existing game to the left to continue playing where you left off.</li>
        <li>View your personal game stats (or log out) via the navigation bar at the top.</li>
        <li>View other users' game stats by clicking on the usernames to the right.</li>
    </ul>
    <br>

<!-- NON-LOGGED IN USER ONLY SEES LOGIN FORM -->
<?php else: ?>
    <p>
        Please log in below to start playing JOTTO!
    </p>
    <p>
        <small>If this is your first time here, please <a href="/user/signup">create an account</a>.</small>
    </p>
    <div id="login">
        <form method="POST" action="/user/profile">
            <table>
                <tr>
                    <td class="prompt">Username:</td>
                    <td><input type="text" name="username" size="20" maxlength="16" required></td>
                </tr>
                <tr>
                    <td class="prompt">Password:</td>
                    <td><input type="password" name="password" size="20" maxlength="16" required></td>
                </tr>
            </table>

            <input type="submit" value="Log in">
        </form>
    </div>

<?php endif; ?>