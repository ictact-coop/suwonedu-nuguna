jQuery(document).ready(function($) {

	$('nav#menu3').mmenu({
	slidingSubmenus: false
	}, {

	});

//	The menu on the right

	var $menu = $('nav#menu-right');
	$menu.mmenu({
		offCanvas	: {
			position	: 'right'
		},

		classes		: 'mm-right',
		dragOpen	: true,
		counters	: false,
		searchfield	: false,
		header		: {
			add			: true,
			update		: true,
			title		: 'Account'
		}
	});

	$('nav#menu').mmenu({
			slidingSubmenus: false
		}, {
	});

//	The menu on the right

	var $menu = $('nav#menu-right');
	$menu.mmenu({
		offCanvas	: {
			position	: 'right'
		},

		classes		: 'mm-right',
		dragOpen	: true,
		counters	: false,
		searchfield	: false,
		header		: {
			add			: true,
			update		: true,
			title		: 'Account'
		}
	});

	if($('#fo_insert_member').length > 0 && $(window).width() < 533) {
		$('#fo_insert_member tr:nth-child(4)').css('height', '110px');
		$('#fo_insert_member input[name="mobile[]"]').css('width', "80px");
	}

	var body_tables = $('.xe_content[class^=document] table:not([class])');
	if(body_tables.length > 0) {
		body_tables.css('width', 'auto').addClass('table-responsive');
	}

});

