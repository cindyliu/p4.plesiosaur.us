<?php

class base_controller {
	
	public $user;
	public $userObj;
	public $template;
	public $email_template;

	/*-------------------------------------------------------------------------------------------------

	-------------------------------------------------------------------------------------------------*/
	public function __construct() {

		# Instantiate User obj
			$this->userObj = new User();
			
		# Authenticate / load user
			$this->user = $this->userObj->authenticate();					
						
		# Set up templates
			$this->template 	  = View::instance('_v_template');
			$this->email_template = View::instance('_v_email');			
								
		# So we can use $user in views			
			$this->template->set_global('user', $this->user);


	    # Get list of games and users for left and right sidebars
	    	if($this->user) {
				$games = $this->get_games($this->user->user_id);

				if(($games == -1) || ($games == -2)) {
					Router::redirect('/index/index/login-needed');
				}
				else {
					$this->template->games = $games;
				}

				$user_list = $this->get_users();

				if($user_list == -1) {
					Router::redirect('/index/index/login-needed');
				}
				else {
					$this->template->user_list = $user_list;
				}
			}

		# Just set the <title> tag to the app name universally
			$this->template->title = APP_NAME;

	}
	
	// GETS LIST OF ALL GAMES LINKED TO THE GIVEN USER_ID
	private function get_games($player_id = NULL) {
		if($player_id == NULL) {
			return (-2);
		}
		elseif(!$this->user) {
			return (-1);
		}
		else {
			$q = 'SELECT *
					FROM games
				   WHERE user_id = '.$player_id.'
					 AND status = "live"';

			$results = DB::instance(DB_NAME)->select_rows($q);

			$games = Array();
			foreach($results as $result) {
				$q = 'SELECT game_id, guess_date, word
						FROM guesses
					   WHERE game_id = '.$result['game_id'].'
					ORDER BY guess_no DESC
					   LIMIT 1';

				$last_guess = DB::instance(DB_NAME)->select_row($q);

				if($last_guess) {
					$game = Array(
						'game_id' => $result['game_id'],
						'last_move' => strtoupper($last_guess['word']),
						'last_move_date' => Time::display($last_guess['guess_date'], 'm-j-y g:ia')
					);
				}
				else {
					$game = Array(
						'game_id' => $result['game_id'],
						'last_move' => NULL,
						'last_move_date' => Time::display($result['date_started'], 'm-j-y g:ia')
					);
				}
				array_push($games, $game);
			}
			return $games;
		}
	}

	// GETS LIST OF ALL USERS EXCEPT LOGGED-IN USER
	private function get_users() {
		if(!$this->user) {
			return (-1);
		}
		else {
			$q = 'SELECT username
					FROM users
				   WHERE user_id != '.$this->user->user_id.'
				ORDER BY username ASC';

			$results = DB::instance(DB_NAME)->select_rows($q);

			$user_list = Array();
			foreach($results as $result) {
				array_push($user_list, $result['username']);
			}
			return $user_list;
		}
	}


} # eoc
