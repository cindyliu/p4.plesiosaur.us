<?php if(isset($profile_user)): ?>

	<?php if($current_username == $user->username): ?>

		<h2>Welcome to your profile, <?=$user->username?>!</h2>
	
	<?php else: ?>
	
		<h2>Profile for:<br>
			<?=$current_username?></h2>
	
		<a href="/game/newgame/<?=$current_username?>">Start new game with <?=$current_username?></a>
		<br><br>
	
	<?php endif; ?>

	....PROFILE STATS AND DATA AND STUFF GOES HERE....<br>

<?php else: ?>

	<h2>User not found</h2>

<?php endif; ?>