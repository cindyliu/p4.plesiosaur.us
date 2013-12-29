<?php

class game_controller extends base_controller {
	
	public function __construct() {
		parent::__construct();
	}

	public function newgame() {

		$secret_word = 'BAGEL';

		$new_game = Array(
			'game_id' => '',
			'user_id' => $this->user->user_id,
			'date_started' => Time::now(),
			'secret_word' => $secret_word,
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

	public function play($game_id = NULL) {

		$q = 'SELECT *
				FROM games
			   WHERE game_id = '.$game_id.'
				 AND status = "live"';

		$game = DB::instance(DB_NAME)->select_row($q);

		$this->template->content = View::instance('v_game_play');

		if($game) {

			$this->template->content->game_data = $game;

			$q = 'SELECT *
					FROM guesses
				   WHERE game_id = '.$game_id;

			$guesses = DB::instance(DB_NAME)->select_rows($q);

			$this->template->guesses = $guesses;

		}

		# CSS/JS includes
			$client_files_head = Array('//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
	    	$this->template->client_files_head = Utils::load_client_files($client_files_head);


	    	$client_files_body = Array('/js/jotto.js');
	    	$this->template->client_files_body = Utils::load_client_files($client_files_body);

		echo $this->template;
	}

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

} # eoc
