
// LITERALLY ALL THIS DOES IS MAKE SURE THE DOTTED LINES GO ALL THE WAY DOWN THE PAGE

var L = $('#left-sidebar').height();
var M = $('#middle-column').height();
var R = $('#right-sidebar').height();

if((L > M) || (R > M)) {
	$('#middle-column').height(((L > R) ? L : R));
};
