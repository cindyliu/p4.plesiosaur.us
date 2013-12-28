<?php if(isset($game_data)): ?>
	<h2>Game #<?=$game_data['game_id']?></h2>
	
<?php else: ?>
	<h2>Error: not an active game</h2>
<?php endif; ?>
