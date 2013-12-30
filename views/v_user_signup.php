<!-- BASIC SIGNUP PAGE, PRETTY SIMILAR TO P2'S -->
<h2>signup</h2>

<?php if(isset($errors)): ?>
    <div class="error">
        <?php foreach($errors as $error) echo $error.'<br>'; ?>
    </div>
    <br>
<?php endif; ?>

<div id="signup">
    <form method="POST" action="/user/signup">
        <table>
            <tr>
                <td class="prompt">Username:</td>
                <td><input type="text" name="username" size="20" maxlength="16" 
                        <?php if(isset($temp_username)) echo 'value="'.$temp_username.'"' ?>
                        required></td>
            </tr>
            <tr>
                <td class="prompt">Password:</td>
                <td><input type="password" name="password" size="20" maxlength="16" required></td>
            </tr>
            <tr>
                <td class="prompt">Re-enter password:</td>
                <td><input type="password" name="pw-check" size="20" maxlength="16" required></td>
            </tr>
        </table>

        <input type="submit" value="Sign up!">
    </form>
</div>