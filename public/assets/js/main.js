import '../css/main.css';

var $ = require('jquery');

$(document).ready(function() {
	$('body').prepend('<h1>'+greet('jill')+'</h1>');
});

import 'bootstrap';

const Swal = require('sweetalert2');

var custom = require('./custom');