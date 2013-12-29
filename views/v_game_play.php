<?php if(isset($game_data)): ?>
	<h2>Game #<?=$game_data['game_id']?></h2>

	<div id="play-area">
		<span class="prompt">PLAY:</span>
		<span id="guess-box">
			<input type="text" name="guessed_word" maxlength="5" required>
		</span>
		<button id="guess-button">Submit guess!</button>
	</div>

	<br><br><br>

	<div id="instructions">
		<h3>How to play JOTTO:</h3>
		Lorem ipsum
	</div>

<?php else: ?>
	<h2>Error: not an active game</h2>

<?php endif; ?>
