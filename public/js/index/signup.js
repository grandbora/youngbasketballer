/**
 * request to user creation
 */
function createUser(type, position)
{
	var teamName = $('.watermarked').val().replace(/ /gi, '');
	$.callAjax({
		url : "/index/createuser",
		dataType : "html",
		data : {
			type : type,
			position : position,
			teamname : teamName
		},
		success : function()
		{
			window.location = '/profile/userschedule';
		}
	});
}

function setTeamOwnerButton(source)
{
	var teamName = $(source).val().replace(/ /gi, '');
	if (false == teamName || 5 > teamName.length)
		$('.button.teamOwner').button("option", "disabled", true);
	else
		$('.button.teamOwner').button("option", "disabled", false);
}

var cfg = ($.hoverintent = {
	sensitivity : 7,
	interval : 100
});
$.event.special.hoverintent = {
	setup : function()
	{
		$(this).bind("mouseover", jQuery.event.special.hoverintent.handler);
	},
	teardown : function()
	{
		$(this).unbind("mouseover", jQuery.event.special.hoverintent.handler);
	},
	handler : function(event)
	{
		event.type = "hoverintent";
		var self = this, args = arguments, target = $(event.target), cX, cY, pX, pY;

		function track(event)
		{
			cX = event.pageX;
			cY = event.pageY;
		}
		;
		pX = event.pageX;
		pY = event.pageY;
		function clear()
		{
			target.unbind("mousemove", track).unbind("mouseout", arguments.callee);
			clearTimeout(timeout);
		}
		function handler()
		{
			if ((Math.abs(pX - cX) + Math.abs(pY - cY)) < cfg.sensitivity) {
				clear();
				jQuery.event.handle.apply(self, args);
			} else {
				pX = cX;
				pY = cY;
				timeout = setTimeout(handler, cfg.interval);
			}
		}
		var timeout = setTimeout(handler, cfg.interval);
		target.mousemove(track).mouseout(clear);
		return true;
	}
};