<?php if($current_username == $user->username): ?>
	<h2>Welcome to your profile, <?=$user->username?>!</h2>
<?php else: ?>
	<h2>Profile for: <?=$current_username?></h2>
<?php endif; ?>

....PROFILE STATS AND DATA AND STUFF GOES HERE....<br>

