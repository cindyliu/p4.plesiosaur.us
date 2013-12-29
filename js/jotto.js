// MAGIC
var ALPHABET = Array(
	'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
	'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
);

var GAME_ID = parseInt($('#game-id').val());

$.get('/wordlist.txt', function(data) {
	wordlist = data.toUpperCase().split(/\n/);
console.log(wordlist);
	$('#left-sidebar').html('<h2>Guesses</h2>');
	$('#right-sidebar').html('<h2>Alphabet</h2>');
	for(var i = 0; i < ALPHABET.length; i++) {
		$('#right-sidebar').append('<div class="alphabet">' + ALPHABET[i] + '</div>');
	};
	$('#right-sidebar').append('<div class="alpha-instruct">Click letters to mark them in black (not in secret word), then green (in secret word), then back to default.</div>');

	$.ajax({
		type: 'POST',
		url: '/game/get_guesses_by_game_id',
		success: function(guesses_json) {
			var guesses = $.parseJSON(guesses_json);
			for(var i = 0; i < guesses.length; i++) {
				$('#left-sidebar').append('<div class="guess">' + guesses[i]['word'] + ': ' + guesses[i]['num_correct'] + '</div>');
			};
		},
		data: {
			'game_id': GAME_ID
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

	$.ajax({
		type: 'POST',
		url: '/game/get_secret_word_by_game_id',
		success: function(secret_word) {
			if(secret_word == '') {
				secret_word = wordlist[Math.floor(Math.random() * wordlist.length)];

				$.ajax({
					type: 'POST',
					url: '/game/set_secret_word_by_game_id',
					success: function(retval) {
						if(retval != 0) {
							alert('Error setting secret word!!!');
							return;
						}
					},
					data: {
						'game_id': GAME_ID,
						'secret_word': secret_word
					}
				});
			}
console.log(secret_word);
			$('#guess-button').click(function() {
				var guess = $('#guess-box').val().toUpperCase();
				var guess_array = guess.split('');

				var check_unique = Array();
				var all_letters_unique = true;
				for(var i = 0; i < guess_array.length; i++) {
					if(check_unique.indexOf(guess_array[i]) < 0) {
						check_unique.push(guess_array[i]);
					}
					else {
						all_letters_unique = false;
						break;
					}
				}
console.log(guess);
console.log(guess_array);
				if((guess.length != 5) || (!/^[A-Z]+$/.test(guess)) || !all_letters_unique) {
					$('#guess-error').html('Your guess must contain exactly 5 unique alphabetical characters.');
				}
				else if(wordlist.indexOf(guess) < 0) {
					$('#guess-error').html('That word is not in our list of valid English words. Please try another.');
				}
				else {
					var sw_array = secret_word.split('');
					var num_correct = 0;
console.log(sw_array);
					for(var i = 0; i < guess_array.length; i++) {
						if(sw_array.indexOf(guess_array[i]) >= 0) {
							num_correct++;
						};
console.log(sw_array.indexOf(guess_array[i]));
					};

					$.ajax({
						type: 'POST',
						url: '/game/add_guess_by_game_id',
						success: function(retval) {
							if(retval != 0) {
								alert('Error adding new guess to database!!!');
								return;
							}
							$('#left-sidebar').append('<div class="guess">' + guess + ': ' + num_correct + '</div>');
							if(guess == secret_word) {
								$('#overlay').html('<div id=\'won\'>CONGRATULATIONS! YOU FOUND THE SECRET WORD, ' + secret_word + '!!!</div>');
								var center = ($('body').height() / 2) - 5;
								$('#overlay').css('height', center + 'px');
								$('#overlay').show();

								$.ajax({
									type: 'POST',
									url: '/game/close_game',
									success: function(retval) {
										if(retval != 0) {
											alert('Error closing game!!!');
											return;
										};
									},
									data: {
										'game_id': GAME_ID
									}
								});
							}
						},
						data: {
							'game_id': GAME_ID,
							'word': guess,
							'num_correct': num_correct
						}
					});

				};
			});
		},
		data: {
			'game_id': GAME_ID
		}
	});

});
