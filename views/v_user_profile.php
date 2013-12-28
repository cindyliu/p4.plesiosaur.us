<?php if(isset($profile_user)): ?>

	<?php if($current_username == $user->username): ?>

		<h2>Welcome to your profile, <?=$user->username?>!</h2>

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

		<a href="/game/newgame">Start New Game</a>

	<?php else: ?>
	
		<h2>Profile for:<br>
			<?=$current_username?></h2>
	
<!--
		<a href="/game/newgame/<?=$current_username?>">Start new game with <?=$current_username?></a>
		<br><br>
-->
	
	<?php endif; ?>

	<h2>Player Stats</h2>
	<ul>
		<li>Current games: <?=$stats['curr_games']?></li>
		<li>Lifetime games: <?=$stats['all_games']?></li>
		<li>Best score: <?=$stats['best_score']?> turns for '<?=$stats['best_word']?>'</li>
		<li>Worst score: <?=$stats['worst_score']?> turns for '<?=$stats['worst_word']?>'</li>
		<li>Player since: <?=$stats['joined']?></li>
	</ul>
	<br>

<?php else: ?>

	<h2>User not found</h2>

<?php endif; ?>