// MAGIC
var ALPHABET = Array(
	'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
	'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
);

var ALPHA_SELECTOR = '.A, .B, .C, .D, .E, .F, .G, .H, .I, .J, .K, .L, .M, .N, .O, .P, .Q, .R, .S, .T, .U, .V, .W, .X, .Y, .Z';

var GAME_ID = parseInt($('#game-id').val());

$.get('/wordlist.txt', function(data) {
	wordlist = data.toUpperCase().split(/\n/);
console.log(wordlist);
console.log(wordlist.length);
	$('#left-sidebar').html('<h2>Guesses</h2>');
	$('#right-sidebar').html('<h2>Alphabet</h2>');
	var right_sidebar_to_append = '<div id="alphabet">';
	for(var i = 0; i < ALPHABET.length; i++) {
		right_sidebar_to_append = right_sidebar_to_append + '<div class="' + ALPHABET[i] + '">' + ALPHABET[i] + '</div>';
	};

	right_sidebar_to_append = right_sidebar_to_append + '</div><div id="reset-alphas">Reset Colors</div>';
	right_sidebar_to_append = right_sidebar_to_append + '<div id="alpha-instruct">Click letters to mark them in black (not in secret word), then green (in secret word), then back to default.</div>';

console.log(right_sidebar_to_append);

	$('#right-sidebar').append(right_sidebar_to_append);

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
console.log(left_sidebar_to_append);
				$('#left-sidebar').append(left_sidebar_to_append);
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
	});

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
console.log(secret_word);
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

function do_guess(sw) {
	var guess = $('#guess-box').val().toUpperCase();
	var guess_array = guess.split('');
	$('#guess-box').val('');
	$('#guess-error').html('');

console.log(guess);
console.log(guess_array);
	if((guess.length != 5) || (!/^[A-Z]+$/.test(guess)) || !unique_letters(guess)) {
		$('#guess-error').html('Your guess must contain exactly 5 unique alphabetical characters.');
	}
	else if(wordlist.indexOf(guess) < 0) {
		$('#guess-error').html('That word is not in our list of valid words. Please try another.');
	}
	else {
		var sw_array = sw.split('');
		var num_correct = 0;
console.log(sw_array);
		for(var i = 0; i < guess_array.length; i++) {
			if(sw_array.indexOf(guess_array[i]) >= 0) {
				num_correct++;
			};
		};

		$.ajax({
			type: 'POST',
			url: '/game/add_guess_by_game_id',
			success: function(retval) {
				if(retval != 0) {
					alert('Error adding new guess to database!!!');
					return;
				}

				var left_sidebar_to_append = '<div class="guess">';
				var colors = Array();
				for(var j = 0; j < guess_array.length; j++) {
					left_sidebar_to_append = left_sidebar_to_append + '<span class="' + guess_array[j] + '">' + guess_array[j] + '</span>';
					colors.push($('#alphabet > .' + guess_array[j]).css('color'));
				}
				left_sidebar_to_append = left_sidebar_to_append + ': ' + num_correct + '</div>';

				$('#left-sidebar').append(left_sidebar_to_append);

				for(j = 0; j < colors.length; j++) {
					$('.' + guess_array[j]).css('color', colors[j]);
				};

				for(var j = 0; j < guess_array.length; j++) {
					$('.guess.' + guess_array[j]).css('color','blue');
				};

				if(guess == sw) {
					$('#content').html('CONGRATULATIONS, YOU FOUND THE SECRET WORD: ' + sw + '!');
					$('#content').css({
						'color': 'yellow',
						'font-size': 'xx-large',
						'width': '500px',
						'height': $(document).height(),
						'margin': '2em auto',
					});

					$(document).off('click', ALPHA_SELECTOR);

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
