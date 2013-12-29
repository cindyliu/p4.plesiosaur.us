// MAGIC
$('#left-sidebar').html('<h2>Guesses</h2>');

$.ajax({
	type: 'POST',
	url: '/game_logic.php',
	success: function(guesses) {
		guesses.each(function() {
			$('#left-sidebar').append(guesses['word'] + ': ' + guesses['num_correct']);
		});
		$('#left-sidebar').html('<br>');
	},
	data: {
		'game_id': <?php echo $content->game_data['game_id']; ?>
	}
});

$('#guess-button').mousedown(function() {
	$('#guess-button').css('border-top','solid .25em #360');
	$('#guess-button').css('border-left','solid .25em #360');
	$('#guess-button').css('border-bottom','solid .25em #9c6');
	$('#guess-button').css('border-right','solid .25em #9c6');
	$('#guess-error').html('');
});

$('#guess-button').mouseup(function() {
	$('#guess-button').css('border-top','solid .25em #9c6');
	$('#guess-button').css('border-left','solid .25em #9c6');
	$('#guess-button').css('border-bottom','solid .25em #360');
	$('#guess-button').css('border-right','solid .25em #360');
});

$('#guess-button').click(function() {
	var guess = $('#guess-box').val().toUpperCase();
	var guess_explode = guess.split('');

	console.log(guess);
	console.log(guess_explode);

	if((guess.length != 5) || (!/^[A-Z]+$/.test(guess))) {
		$('#guess-error').html('Guess must contain exactly 5 alphabetic characters.');
	};

});

