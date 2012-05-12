$(document).ready(function()
{
	window.fbAsyncInit = function()
	{
		FB.init({
			appId : fbAppId,
			channelUrl : '/js/channel.php',
			status : true,
			cookie : true
		});

		// fire trigger
		$(document).trigger('fbLoaded.yb');
	};
	(function(d)
	{
		var js, id = 'facebook-jssdk';
		if (d.getElementById(id)) {
			return;
		}
		js = d.createElement('script');
		js.id = id;
		js.async = true;
		js.src = "//connect.facebook.net/en_US/all.js";
		d.getElementsByTagName('head')[0].appendChild(js);
	}(document));
});