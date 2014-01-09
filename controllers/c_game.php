<?php
// ALL GAMEPLAY FUNCTIONS IN THIS CONTROLLER
class game_controller extends base_controller {
	
	public function __construct() {
		parent::__construct();

		// IF NOT LOGGED IN, CAN'T DO GAMES
		if(!$this->user) {
			Router::redirect('/index/index/login-needed');
		}
	}

	// START A NEW GAME:
	// GENERATE NEW GAME DATA, LOAD INTO DATABASE, THEN
	// REROUTE TO GAMEPLAY PAGE SO THE USER CAN START
	// PLAYING THIS GAME IMMEDIATELY
	public function newgame() {

		$new_game = Array(
			'game_id' => '',
			'user_id' => $this->user->user_id,
			'date_started' => Time::now(),
			'secret_word' => NULL,
			'last_played' => Time::now(),
			'status' => 'live',
			'num_guesses' => 0
		);

		$newgame_id = DB::instance(DB_NAME)->insert_row('games', $new_game);

		if($newgame_id) {
			Router::redirect('/game/play/'.$newgame_id);
		}
		else {
			Router::redirect('/index/index/newgame-error');
		}
	}

	// GAMEPLAY. PRETTY SIMPLE BASE PAGE; ALL GAME LOGIC VIA JAVASCRIPT
	public function play($game_id = NULL) {

		$q = 'SELECT *
				FROM games
			   WHERE game_id = '.$game_id;

		$game = DB::instance(DB_NAME)->select_row($q);

		$this->template->content = View::instance('v_game_play');

		if($game) {

			$this->template->content->game_data = $game;

			$q = 'SELECT *
					FROM guesses
				   WHERE game_id = '.$game_id;

			$guesses = DB::instance(DB_NAME)->select_rows($q);

			$this->template->guesses = $guesses;

		# CSS/JS includes
	    	$client_files_body = Array('/js/jotto.js', '/js/equalize_column_heights.js');
	    	$this->template->client_files_body = Utils::load_client_files($client_files_body);
	    }

		echo $this->template;
	}


	public function get_game_data() {

		$q = 'SELECT *
				FROM games
			   WHERE game_id = '.$_POST['game_id'];

		$game_data = DB::instance(DB_NAME)->select_row($q);
		$game_data['last_played'] = Time::display($game_data['last_played'], 'F j, Y g:ia');

		if($game_data) {
			echo json_encode($game_data);
		}
		else {
			echo '';
		}

	}

	// FUNCTION TO HANDLE AJAX CALL TO GET LIST OF GUESSES IN THIS GAME
	public function get_guesses_by_game_id() {
		$q = 'SELECT guess_no, word, num_correct
				FROM guesses
			   WHERE game_id = '.$_POST['game_id'].'
			ORDER BY guess_no ASC';

		$results = DB::instance(DB_NAME)->select_rows($q);

		$guesses = Array();
		foreach($results as $result) {
			array_push($guesses, Array(
				'word' => $result['word'],
				'num_correct' => $result['num_correct']
			));
		}

		echo json_encode($guesses);
	}

	// FUNCTION TO HANDLE AJAX CALL TO INSERT NEW VALID USER GUESSES INTO DATABASE
	public function add_guess_by_game_id() {
		$new_guess = Array(
			'game_id' => $_POST['game_id'],
			'guess_no' => '',
			'guess_date' => Time::now(),
			'word' => $_POST['word'],
			'num_correct' => $_POST['num_correct']
		);

		$success = DB::instance(DB_NAME)->insert_row('guesses', $new_guess);

		if($success) {
			echo 0;
		}
		else {
			echo -1;
		}
	}

	// FUNCTION TO HANDLE AJAX CALL TO RETRIEVE GAME'S SECRET WORD, IF IT EXISTS
	public function get_secret_word_by_game_id() {
		$q = 'SELECT secret_word
				FROM games
			   WHERE game_id = '.$_POST['game_id'];

		$secret_word = DB::instance(DB_NAME)->select_field($q);

		if($secret_word) {
			echo $secret_word;
		}
		else {
			echo '';
		};
	}

	// FUNCTION TO HANDLE AJAX CALL TO SET THE GAME'S SECRET WORD
	// IF IT DOESN'T ALREADY HAVE ONE
	public function set_secret_word_by_game_id() {
		$wc = 'WHERE game_id = '.$_POST['game_id'];

		$q = 'SELECT *
				FROM games
				'.$wc;
		
		$game_data = DB::instance(DB_NAME)->select_row($q);

		if($game_data['secret_word'] != NULL) {
			echo -1;
		}

		$game_data['secret_word'] = $_POST['secret_word'];

		DB::instance(DB_NAME)->update('games', $game_data, $wc);

		echo 0;
	}

	// FUNCTION TO HANDLE AJAX CALL TO LET THE DATABASE KNOW WHEN A GAME
	// HAS BEEN COMPLETED
	public function close_game() {
		$wc = 'WHERE game_id = '.$_POST['game_id'];

		$q = 'SELECT *
				FROM games
				'.$wc;

		$game_data = DB::instance(DB_NAME)->select_row($q);

		if($game_data['status'] != 'live') {
			echo -1;
			return;
		}

		$q = 'SELECT guess_no
				FROM guesses
				'.$wc;

		$num_guesses = count(DB::instance(DB_NAME)->select_rows($q));

		$game_data['num_guesses'] = $num_guesses;
		$game_data['status'] = 'closed';

		$success = DB::instance(DB_NAME)->update('games', $game_data, $wc);

		if($success) {
			echo 0;
		}
		else {
			echo -1;
		}
	}

} # eoc
