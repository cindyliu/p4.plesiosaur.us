<?php

class index_controller extends base_controller {
	
	/*-------------------------------------------------------------------------------------------------

	-------------------------------------------------------------------------------------------------*/
	public function __construct() {
		parent::__construct();
	} 
		
	/*-------------------------------------------------------------------------------------------------
	Accessed via http://localhost/index/index/
	-------------------------------------------------------------------------------------------------*/
	public function index($message = NULL) {

		if($_POST) {
			$_POST = DB::instance(DB_NAME)->sanitize($_POST);
			$_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);

			$q = 'SELECT token
					FROM users
				   WHERE username = "'.$_POST['username'].'"
					 AND password = "'.$_POST['password'].'"';

			$token = DB::instance(DB_NAME)->select_field($q);

			if($token) {
				setcookie('token', $token, strtotime('+1 month'), '/');
	    		Router::redirect('/index/index/logged_in');
			}
			else {
				Router::redirect('/index/index/failed');
			}
		}
		
		# Any method that loads a view will commonly start with this
		# First, set the content of the template with a view file
			$this->template->content = View::instance('v_index_index');

			switch($message) {
				case 'signed_up':
					if($this->user) {
						$this->template->message = 'Congratulations! You are now signed up.';
					}
					break;
				case 'logged_in':
					if($this->user) {
						$this->template->message = 'Successfully logged in!';
					}
					break;
				case 'logged_out':
					if(!$this->user) {
						$this->template->message = 'You have now logged out.';
					}
					break;
				case 'login-needed':
					if(!$this->user) {
						$this->template->message = 'You must be logged in to access this page.';
					}
					break;
				case 'login-not-needed':
					if($this->user) {
						$this->template->message = 'You are already logged in!';
					}
					break;
				case 'failed':
					if(!$this->user) {
						$this->template->message = 'Login failed. Please try again.';
					}
					break;
				case 'newgame-error':
					if($this->user) {
						$this->template->message = 'There was an error creating your game. Please try again.';
					}
					break;
				default:
			}
			
		# Now set the <title> tag
			$this->template->title = APP_NAME;
	
		# CSS/JS includes
			$client_files_head = Array(
				"//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js",
				"//cdn.jquerytools.org/1.2.5/full/jquery.tools.min.js");
	    	$this->template->client_files_head = Utils::load_client_files($client_files_head);


	    	$client_files_body = Array("js/login.js");
	    	$this->template->client_files_body = Utils::load_client_files($client_files_body);


		# Render the view
			echo $this->template;

	} # End of method

} # End of class
