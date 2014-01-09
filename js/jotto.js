// GLOBAL VARS
var ALPHABET = Array(
	'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
	'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
);

var ALPHA_SELECTOR = '.A, .B, .C, .D, .E, .F, .G, .H, .I, .J, .K, .L, .M, .N, .O, .P, .Q, .R, .S, .T, .U, .V, .W, .X, .Y, .Z';

var GAME_ID = parseInt($('#game-id').val());

// TEST FOR CLOSED GAME:
// IF GAME IS CLOSED, DISPLAY ARCHIVE MESSAGE AND GUESS HISTORY BUT DO NOT LOAD GAME.
// IF GAME IS LIVE, COMPLETE GAME SETUP.
$.ajax({
	type: 'POST',
	url: '/game/get_game_data',
	success: function(game_data_json) {
		if(game_data_json == '') {
			alert('Error: game data could not be retrieved.');
			console.log('Error on game_id = ' + GAME_ID);
		}
		else {
			var game_data = $.parseJSON(game_data_json);
			if(game_data['status'] == 'closed') {
				display_game_sidebars('closed');

				$('#content').html('<h2>GAME #' + GAME_ID + ': ARCHIVED</h2>Solved ' + game_data['last_played'] + '<br>after ' + game_data['num_guesses'] + ' guesses');
				$('#content').css({
					'color': 'rgb(102, 170, 248)',
					'width': '500px',
					'height': $(document).height(),
					'margin': '2em auto',
				});
				$('#content h2').css({
					'color': 'rgb(102, 170, 255)',
					'font-size': 'xx-large'
				});
			}
			else {
				do_game();
			};
		};
	},
	data: {
		'game_id': GAME_ID
	}
});


// PUT GAME INTO SEPARATE FUNCTION TO AVOID TOO MUCH NESTING
function do_game() {
	// LOAD WORDLIST INTO ARRAY. SINCE THIS MAY TAKE A LITTLE WHILE,
	// ALL OTHER GAME LOGIC IS IN ITS CALLBACK FUNCTION
	$.get('/wordlist.txt', function(data) {
		wordlist = data.toUpperCase().split(/\n/);

		display_game_sidebars('live');

		// BUTTON ANIMATION
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
		});

		// ALPHABET LETTER COLOR CHANGING HANDLER
		$(document).on('click', ALPHA_SELECTOR, function() {
			var classes = $(this).attr('class');
			var this_class = classes[(classes.length - 1)];
			if($(this).css('color') == 'rgb(238, 238, 204)') {
				$('.' + this_class).css('color', 'rgb(0, 0, 0)');
			}
			else if($(this).css('color') == 'rgb(0, 0, 0)') {
				$('.' + this_class).css('color', 'rgb(0, 255, 0)');
			}
			else {
				$('.' + this_class).css('color', 'rgb(238, 238, 204)');
			}
		});

		$('#reset-alphas').click(function() {
			$(ALPHA_SELECTOR).css('color', 'rgb(238, 238, 204)');
		});

		// GET SECRET WORD, SET IF NECESSARY (IF NEWGAME)
		$.ajax({
			type: 'POST',
			url: '/game/get_secret_word_by_game_id',
			success: function(secret_word) {
				if(secret_word == '') {
					do {
						secret_word = wordlist[Math.floor(Math.random() * wordlist.length)];
					} while (!unique_letters(secret_word));

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

				$('#guess-button').click(function() {
					do_guess(secret_word);
				});
				$('#guess-box').keypress(function(event) {
					if(event.which == 13) {
						do_guess(secret_word);
					}
				});
			},
			data: {
				'game_id': GAME_ID
			}
		});

	});
}


// FUNCTION TO CHECK WORD FOR UNIQUE LETTERS
function unique_letters(word) {
	var word_array = word.split('');

	var check_unique = Array();
	var all_letters_unique = true;
	for(var i = 0; i < word_array.length; i++) {
		if(check_unique.indexOf(word_array[i]) < 0) {
			check_unique.push(word_array[i]);
		}
		else {
			all_letters_unique = false;
			break;
		}
	}

	return all_letters_unique;
}


// REPLACES THE GAMES LIST AND USER LIST OF THE LEFT AND RIGHT SIDEBARS
// WITH THE GUESS HISTORY AND ALPHABET FOR GAMEPLAY
function display_game_sidebars(status) {
	// CHANGE LEFT AND RIGHT SIDEBARS TO GUESS LIST AND ALPHABET
	$('#left-sidebar').html('<h2>Guesses</h2>');
	$('#right-sidebar').html('<h2>Alphabet</h2>');
	var right_sidebar_to_append = '<div id="alphabet">';
	for(var i = 0; i < ALPHABET.length; i++) {
		right_sidebar_to_append = right_sidebar_to_append + '<div class="' + ALPHABET[i] + '">' + ALPHABET[i] + '</div>';
	};

	if(status == 'live') {
		right_sidebar_to_append = right_sidebar_to_append + '</div><div id="reset-alphas">Reset Colors</div>';
		right_sidebar_to_append = right_sidebar_to_append + '<div id="alpha-instruct">Click letters to mark them in black (not in secret word), then green (in secret word), then back to default.</div>';
	};

	$('#right-sidebar').append(right_sidebar_to_append);

	// GET GUESS LIST AND PUT IT INTO LEFT SIDEBAR
	$.ajax({
		type: 'POST',
		url: '/game/get_guesses_by_game_id',
		success: function(guesses_json) {
			var guesses = $.parseJSON(guesses_json);
			for(var i = 0; i < guesses.length; i++) {
				var guess_split = guesses[i]['word'].split('');

				var left_sidebar_to_append = '<div class="guess">';
				for(var j = 0; j < guess_split.length; j++) {
					left_sidebar_to_append = left_sidebar_to_append + '<span class="' + guess_split[j] + '">' + guess_split[j] + '</span>';
				}
				left_sidebar_to_append = left_sidebar_to_append + ': ' + guesses[i]['num_correct'] + '</div>';

				$('#left-sidebar').append(left_sidebar_to_append);
			};

			if(status == 'closed') {
				$('#alphabet, .guess').css('cursor', 'default');
			};
		},
		data: {
			'game_id': GAME_ID
		}
	});
}


// HANDLES USER GUESS INPUT
function do_guess(sw) {
	var guess = $('#guess-box').val().toUpperCase();
	var guess_array = guess.split('');
	$('#guess-box').val('');
	$('#guess-error').html('');

	// ERROR CHECKING ON GUESS INPUT
	if((guess.length != 5) || (!/^[A-Z]+$/.test(guess)) || !unique_letters(guess)) {
		$('#guess-error').html('Your guess must contain exactly 5 unique alphabetical characters.');
	}
	else if(wordlist.indexOf(guess) < 0) {
		$('#guess-error').html('That word is not in our list of valid words. Please try another.');
	}
	else {
		// CALCULATE NUMBER OF LETTERS COMMON TO USER GUESS WORD AND SECRET WORD
		var sw_array = sw.split('');
		var num_correct = 0;

		for(var i = 0; i < guess_array.length; i++) {
			if(sw_array.indexOf(guess_array[i]) >= 0) {
				num_correct++;
			};
		};

		// LOAD GUESS INTO DATABASE IF VALID
		$.ajax({
			type: 'POST',
			url: '/game/add_guess_by_game_id',
			success: function(retval) {
				if(retval != 0) {
					alert('Error adding new guess to database!!!');
					return;
				}

				// ADD GUESS TO LIST OF GUESSES IN LEFT SIDEBAR
				var left_sidebar_to_append = '<div class="guess">';
				var colors = Array();
				for(var j = 0; j < guess_array.length; j++) {
					left_sidebar_to_append = left_sidebar_to_append + '<span class="' + guess_array[j] + '">' + guess_array[j] + '</span>';
					colors.push($('#alphabet > .' + guess_array[j]).css('color'));
				}
				left_sidebar_to_append = left_sidebar_to_append + ': ' + num_correct + '</div>';

				$('#left-sidebar').append(left_sidebar_to_append);

				// NEW GUESSES NEED TO INHERIT LETTER COLORS CHANGED BY USER
				for(j = 0; j < colors.length; j++) {
					$('.' + guess_array[j]).css('color', colors[j]);
				};

				// HANDLES USER WIN
				if(guess == sw) {
					// DISPLAY CONFIRMATION MESSAGE
					$('#content').html('CONGRATULATIONS, YOU FOUND THE SECRET WORD: ' + sw + '!');
					$('#content').css({
						'color': 'yellow',
						'font-size': 'xx-large',
						'width': '500px',
						'height': $(document).height(),
						'margin': '2em auto',
					});
					$('#content').append('<p><a href="/game/newgame">PLAY AGAIN</a></p>');

					// TURN OFF ALPHABET COLOR CHANGING
					$(document).off('click', ALPHA_SELECTOR);

					// CLOSE GAME
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
}

