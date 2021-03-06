<?php

// ALL USER FUNCTIONS IN THIS CONTROLLER
class user_controller extends base_controller {
	
	public function __construct() {
		parent::__construct();
	} 

	// SIGNUP PAGE VIEW AND PROCESSING
	public function signup() {

        // Generate view, initialize errors
        $this->template->content = View::instance('v_user_signup');
		$errors = Array();
		$error_flag = false;

		// First time to page, not processing any data, just display
		if(!$_POST) {
	    	echo $this->template;
	    	return;
		}

		// Sanitization
		$_POST = DB::instance(DB_NAME)->sanitize($_POST);

		// Checking for existing username
		$q = 'SELECT user_id
		      FROM users
		      WHERE username = "'.$_POST['username'].'"
		      LIMIT 1';

		$username_exists = DB::instance(DB_NAME)->select_field($q);

		// If errors, add them to the errors array
		if($username_exists) {
	    	$error_flag = true;
	        array_push($errors,'Username is taken - please choose another.');
		}

		// Prevent blank fields. This is also being done client-side, but in case
		//   user's browser is antiquated, done server-side anyway
		foreach($_POST as $prompt => $value) {
		    if(trim($value) == '') {
	    	    $error_flag = true;
				array_push($errors, $prompt.' cannot be blank.');
	    	}
		}

		// Implementation of password re-entry confirmation
		if($_POST['password'] != $_POST['pw-check']) {
	    	$error_flag = true;
	    	array_push($errors, 'Password entries did not match.');
		}

		// Limit usernames and passwords to 16 characters
		$check_len = strlen($_POST['username']);
		if(($check_len > 16) || ($check_len < 2)) {
			$error_flag = true;
			array_push($errors, 'Usernames must be 2-16 alphanumeric characters.');
		}

		$check_len = strlen($_POST['password']);
		if(($check_len > 16) || ($check_len < 8)) {
			$error_flag = true;
			array_push($errors, 'Passwords must be 8-16 alphanumeric characters.');
		}		

		// Send off the errors
		$this->template->content->errors = $errors;

		$this->template->content->temp_username = $_POST['username'];	

		// If there was an error, don't process any more, just make them do it again
		if($error_flag) {
	    	echo $this->template;
		}
		// Otherwise, put them into the database
		else {
	    	unset($_POST['pw-check']);
	    	$_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);
	    	$_POST['token'] = sha1(TOKEN_SALT.$_POST['username'].Utils::generate_random_string());
	    	$_POST['joined'] = Time::now();
	    	$_POST['last_login'] = $_POST['joined'];

            $new_user = DB::instance(DB_NAME)->insert_row('users', $_POST);

	    	// This implements auto-login upon signup
            if($new_user) {
	        	setcookie('token', $_POST['token'], strtotime('+1 month'), '/');
	    	}
	    	Router::redirect('/index/index/signed_up');
		}
	}

	// USER PROFILE VIEW WITH STATS
	public function profile($current_username = NULL) {

		$current_username = DB::instance(DB_NAME)->sanitize($current_username);

		// I USED TO HAVE SIGNUP FORM LEAD TO PROFILE PAGE; NOT ANYMORE
		// BUT I LEFT THIS HERE ANYWAY
		if($_POST) {
			if((trim($_POST['username']) == '') || (trim($_POST['password']) == '')) {
    			Router::redirect('/index/index/failed');
    		}

    		$_POST = DB::instance(DB_NAME)->sanitize($_POST);

    		$_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);

    		$q = 'SELECT token
			      FROM users
			      WHERE username = "'.$_POST['username'].'"
			      AND password = "'.$_POST['password'].'"';

			$token = DB::instance(DB_NAME)->select_field($q);


    		$q = 'SELECT username
			      FROM users
			      WHERE token = "'.$token.'"';

			if($current_username == NULL) {
				$current_username = DB::instance(DB_NAME)->select_field($q);
			}

			if($token) {
	    		setcookie('token', $token, strtotime('+1 month'), '/');
	    		Router::redirect('/user/profile/'.$current_username);
			}
			else {
				Router::redirect('/index/index/failed');
			}
		}
		// DIFFERENT WELCOME MESSAGES BASED ON IF THIS IS YOUR PROFILE OR
		// ANOTHER USER'S PROFILE; GETS USER GAME STATS
		else {
			if(!$this->user) {
				Router::redirect('/index/index/login-needed');
			}

			if($current_username == NULL) {
				$current_username = $this->user->username;
			}

			$q = 'SELECT *
					FROM users
				   WHERE username = "'.$current_username.'"';

			$profile_user = DB::instance(DB_NAME)->select_row($q);

			$this->template->content = View::instance('v_user_profile');

			if($profile_user) {
				$current_username = $profile_user['username'];
				$this->template->content->current_username = $current_username;
				$this->template->content->profile_user = $profile_user;
			}

			$curr_games = 'SELECT game_id
							 FROM games
							WHERE user_id = '.$profile_user['user_id'].'
							  AND status = "live"';
			
			$old_games = 'SELECT secret_word, num_guesses
							FROM games
						   WHERE user_id = '.$profile_user['user_id'].'
							 AND status = "closed"
						ORDER BY last_played ASC';

			$join_date = 'SELECT joined
							FROM users
						   WHERE user_id = '.$profile_user['user_id'];

			$curr_games = count(DB::instance(DB_NAME)->select_rows($curr_games));
			$old_games = DB::instance(DB_NAME)->select_rows($old_games);
			$join_date = DB::instance(DB_NAME)->select_field($join_date);

			$all_games = $curr_games + count($old_games);

			$best_score = 1000;
			$best_word = '00000';
			$worst_score = 0;
			$worst_word = '00000';

			foreach($old_games as $old_game) {
				if($old_game['num_guesses'] <= $best_score) {
					$best_score = $old_game['num_guesses'];
					$best_word = $old_game['secret_word'];
				}
				if($old_game['num_guesses'] >= $worst_score) {
					$worst_score = $old_game['num_guesses'];
					$worst_word = $old_game['secret_word'];
				}
			}

			$this->template->content->stats = Array(
				'curr_games' => $curr_games,
				'all_games' => $all_games,
				'best_score' => $best_score,
				'best_word' => $best_word,
				'worst_score' => $worst_score,
				'worst_word' => $worst_word,
				'joined' => Time::display($join_date, 'F j, Y')
			);

			echo $this->template;
		}
	}

	// BASIC LOGOUT FUNCTION
	public function logout() {
		if(!$this->user) {
			Router::redirect('/index/index/login-needed');
		}

		$new_token = Array(
			'token' => sha1(TOKEN_SALT.$this->user->username.Utils::generate_random_string())
		);
		DB::instance(DB_NAME)->update('users', $new_token, 'WHERE user_id = '.$this->user->user_id);

		setcookie('token', '', strtotime('-1 month'), '/');

		Router::redirect('/index/index/logged_out');
	}


} #eoc
