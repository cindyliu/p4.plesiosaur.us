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

		# CSS/JS includes
		$client_files_head = Array(
			"//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js",
			"//cdn.jquerytools.org/1.2.5/full/jquery.tools.min.js");
    	$this->template->client_files_head = Utils::load_client_files($client_files_head);

		# Making sure the columns are all the same height....sigh
    	$client_files_body = Array("/js/equalize_column_heights.js");
		$this->template->client_files_body = Utils::load_client_files($client_files_body);

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
				ORDER BY last_played DESC';

			$results = DB::instance(DB_NAME)->select_rows($q);

			if($results) {
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
							'last_move_date' => Time::display($last_guess['guess_date'], 'm-j-y g:ia'),
							'status' => $result['status']
	 					);
					}
					else {
						$game = Array(
							'game_id' => $result['game_id'],
							'last_move' => NULL,
							'last_move_date' => Time::display($result['date_started'], 'm-j-y g:ia'),
							'status' => $result['status']
						);
					}
					array_push($games, $game);
				}
				return $games;
			}
			else {
				return NULL;
			}
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
