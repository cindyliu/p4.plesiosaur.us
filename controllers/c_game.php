<?php

class game_controller extends base_controller {
	
	public function __construct() {
		parent::__construct();
	}

	public function newgame() {



		// NEED TO FIGURE OUT HOW TO ADD GAME INTO DB

		$this->template->content = View::instance('v_game_new');
		echo $this->template;
	}

	public function play($game_id = NULL) {

		$q = 'SELECT *
				FROM games
			   WHERE game_id = '.$game_id.'
				 AND status = "live"';

		$game = DB::instance(DB_NAME)->select_row($q);

		$this->template->content = View::instance('v_game_play');

		if($game) {

			$this->template->game_data = $game;

			$q = 'SELECT *
					FROM guesses
				   WHERE game_id = '.$game_id;

			$guesses = DB::instance(DB_NAME)->select_rows($q);

			$this->template->guesses = $guesses;
			
		}

		echo $this->template;
	}

} # eoc
