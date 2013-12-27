<?php if(isset($game)): ?>
	<h2>Game with <?=$game['opponent']?></h2>
<?php else: ?>
	<h2>Error: game does not exist</h2>
<?php endif; ?>
