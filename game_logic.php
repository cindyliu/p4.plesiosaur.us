<?php

$_POST['game_id']

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

return $guesses;

/*
$_POST['current_guess']
*/

?>