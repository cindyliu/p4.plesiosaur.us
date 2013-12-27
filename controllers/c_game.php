<?php

class game_controller extends base_controller {
	
	public function __construct() {
		parent::__construct();
	}

	public function newgame($opponent = NULL) {

		// NEED TO FIGURE OUT HOW TO ADD GAME INTO DB

		$this->template->content = View::instance('v_game_new');
		echo $this->template;
	}

	public function play($game_id = NULL) {

		$q = 'SELECT *
				FROM games
			   WHERE game_id = "'.$game_id.'"';

		$game = DB::instance(DB_NAME)->select_row($q);

		$this->template->content = View::instance('v_game_play');
		
		if($game) {

			$this->template->content->game = $game;

		}

		echo $this->template;
	}

} # eoc
