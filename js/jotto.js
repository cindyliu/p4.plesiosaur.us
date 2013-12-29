// MAGIC
var alphabet = Array(
	'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
	'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
);

$.get('/wordlist.txt', function(data) {
	var wordlist = data.split(/\n/);
	console.log(wordlist);
});

$('#left-sidebar').html('<h2>Guesses</h2>');
$('#right-sidebar').html('<h2>Alphabet</h2>');
for(var i = 0; i < alphabet.length; i++) {
	$('#right-sidebar').append('<div class="alphabet">' + alphabet[i] + '</div>');
};
$('#right-sidebar').append('<div class="alpha-instruct">Click letters to mark them in black, then green, then back to default.</div>');

$.ajax({
	type: 'POST',
	url: '/game/get_guesses_by_game_id',
	success: function(guesses_json) {
		var guesses = $.parseJSON(guesses_json);
		for(var i = 0; i < guesses.length; i++) {
			$('#left-sidebar').append('<div class="guess">' + guesses[i]['word'] + ': ' + guesses[i]['num_correct'] + '</div>');
		};
		$('#left-sidebar').append('<br>');
	},
	data: {
		'game_id': parseInt($('#game-id').val())
	}
});

$('#guess-button').mousedown(function() {
	$(this).css('border-top','solid .25em #360');
	$(this).css('border-left','solid .25em #360');
	$(this).css('border-bottom','solid .25em #9c6');
	$(this).css('border-right','solid .25em #9c6');
});

$('#guess-button').mouseup(function() {
	$(this).css('border-top','solid .25em #9c6');
	$(this).css('border-left','solid .25em #9c6');
	$(this).css('border-bottom','solid .25em #360');
	$(this).css('border-right','solid .25em #360');
	$('#guess-error').html('');
});

$('#guess-button').click(function() {
	var guess = $('#guess-box').val().toUpperCase();
	var guess_explode = guess.split('');

	console.log(guess);
	console.log(guess_explode);

	if((guess.length != 5) || (!/^[A-Z]+$/.test(guess))) {
		$('#guess-error').html('Your guess must contain exactly 5 unique alphabetical characters.');
	};

});

$('.alphabet').click(function() {
	if($(this).css('color') == 'rgb(238, 238, 204)') {
		$(this).css('color', 'rgb(0, 0, 0)');
	}
	else if($(this).css('color') == 'rgb(0, 0, 0)') {
		$(this).css('color', 'rgb(0, 255, 0)');
	}
	else {
		$(this).css('color', 'rgb(238, 238, 204)');
	}
});
