<?php if(isset($game_data)): ?>
	<h2>Game #<?=$game_data['game_id']?></h2>

	<div id="play-area">
		<span class="prompt">Enter guess here:</span><br>
		<input id="guess-box" type="text" name="guessed_word" maxlength="5" required><br>
		<input id="game-id" type="hidden" value="<?=$game_data['game_id']?>">
		<div id="guess-error"></div>
		<button id="guess-button">Submit guess!</button>
	</div>

	<br>

	<h3>How to play JOTTO:</h3>
	<div id="instructions">
		<p>
			The object of the game is to guess the computer's secret word.
			The computer's word will be a valid English word with 5 unique letters.
		</p>
		<p>
			To play, enter a 5-letter word in the box above.
			It, too, must be a valid English word and cannot contain repeated letters.
			Valid guesses as well as your guess history will be displayed to the left.
		</p>
		<p>
			The computer will compare your word with its secret word and let you know how many correct letters your guess contains, also shown at left.
			Based on the computer's answers, use your logic skills to discover the secret word!
		</p>
		<p>
			For example: if the secret word is PEARS and your guess is MANGO, the computer's response will be 1, since PEARS and MANGO have exactly 1 letter in common.
		</p>
		<p>
			If you're stuck, a simple strategy is to alter your guesses one letter at a time and see how the numbers change.
			For example, if your guess MATCH has 3 correct letters and your next guess LATCH has only 2 correct letters, then you know that the secret word contains an M and does not contain an L.
			You can make note of which letters you have determined are or are not in the secret word by clicking on the letters in the alphabet to the right.
		</p>
	</div>
	~<br><br>
<?php else: ?>
	<h2>Error: not an active game</h2>
<?php endif; ?>
