
jQuery(document).ready(function() {
	$("a[rel=group]").fancybox({
	'transitionIn'		: 'fade',
	'transitionOut'		: 'fade',
	'overlayColor'		: '#000',
	'titlePosition'		: 'outside',
	'overlayOpacity'	: 0.8,
	'titleFormat'		: function(title, currentArray, currentIndex, currentOpts) {
	return '<span id="fancybox-title-over">Фотография ' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? ' &nbsp;<br> ' + title : '') + '</span>';
		}
	});
});
