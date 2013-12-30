<!-- VALID USER: DISPLAY PROFILE -->
<?php if(isset($profile_user)): ?>

	<!-- PROFILE VIEW FOR CURRENT USER -->
	<?php if($current_username == $user->username): ?>

		<h2>Welcome to your profile, <?=$user->username?>!</h2>

		<!-- OLD CODE FROM WHEN I WAS GOING TO HAVE PLAYER VS. PLAYER GAMES -->
		<?php if(isset($newgames)): ?>
			<?php foreach($newgames as $newgame): ?>
				<?=$newgame['opponent']?> would like to start a game with you!
				Accept? <a href="/game/play/<?=$newgame['game_id']?>">YES</a>    
						<a href="/game/deny/<?=$newgame['game_id']?>">NO</a>
				<br>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php if(isset($notices)): ?>
			<?php foreach($notices as $notice) echo $notice; ?>
		<?php endif; ?>

		<!-- LINK FOR NEWGAME -->
		<a href="/game/newgame">Start New Game</a>

	<?php else: ?>
	

		<h2>Profile for:<br>
			<?=$current_username?></h2>

<!-- AGAIN, OLD CODE. LEAVING IT IN SO I CAN KEEP WORKING ON IT LATER -->
<!--
		<a href="/game/newgame/<?=$current_username?>">Start new game with <?=$current_username?></a>
		<br><br>
-->
	<!-- PROFILE VIEW FOR OTHER USERS -->
	<?php endif; ?>

	<h2>Player Stats</h2>
	<ul>
		<li>Current games: <?=$stats['curr_games']?></li>
		<li>Lifetime games: <?=$stats['all_games']?></li>
		<li>Best score:
			<?php if($stats['curr_games'] < $stats['all_games']): ?>
				<?=$stats['best_score']?> turns for '<?=$stats['best_word']?>'
			<?php else: ?>
				<small>(no completed games yet)</small>
			<?php endif; ?>
			</li>
		<li>Worst score:
			<?php if($stats['curr_games'] < $stats['all_games']): ?>
				<?=$stats['worst_score']?> turns for '<?=$stats['worst_word']?>'
			<?php else: ?>
				<small>(no completed games yet)</small>
			<?php endif; ?>
			</li>
		<li>Player since: <?=$stats['joined']?></li>
	</ul>
	<br>

<?php else: ?>

	<!-- INVALID USER PARAMETER -->
	<h2>User not found</h2>

<?php endif; ?>
