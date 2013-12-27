<!-- HOME PAGE -->

<?php if(isset($message)): ?>
    <div class="user-message">
        <?=$message?>
    </div>
<?php endif; ?>

<?php if($user): ?>

    <h2>Hello, <?=$user->username?>!</h2>
    <h3>Updates</h3>
    <ul>
        <li>No updates currently.</li>
    </ul>

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