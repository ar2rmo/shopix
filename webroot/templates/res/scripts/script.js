jQuery( document ).ready(function() {

	if(jQuery(".plusesLink .pluses").length)
	{
		jQuery(".plusesLink .pluses").fancybox({type: 'iframe', 
			href: '/templates/pluses.html', 
			height: 320,
			width: 450,
			fitToView: false,
			autoSize: false,
			topRatio: 0.2,
		});
	}

});