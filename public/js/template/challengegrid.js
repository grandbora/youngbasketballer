/**
 * Sets up the but tons for challenge grid
 */
function setUpChallengeGridButtons()
{
	$('.buttonView.createChallengeButton').each(function(index)
	{
		/*
		 * Sets up createChallengeButton
		 */
		var button = $(this);
		var buttonData = button.data('data');
		var optionList = {
			action : function()
			{
				$.callAjax({
					url : buttonData.postUrl,
					success : function()
					{
						window.location.reload();
					}
				});
			}
		};
		button.buttonView('setOption', optionList);

	});

	$('.buttonView.acceptChallengeButton').each(function(index)
	{
		/*
		 * Sets up acceptChallengeButton
		 */
		var button = $(this);
		var buttonData = button.data('data');
		var optionList = {
			prepareDialog : function(dialogElement)
			{
			},
			dialogOptionList : {
				buttons : [ {
					text : buttonData.title,
					click : function()
					{
						$.callAjax({
							url : buttonData.postUrl,
							data : {
								id : buttonData.gameId
							},
							success : function()
							{
								window.location = buttonData.successUrl;
							}
						});
					}
				} ]
			}
		};
		button.buttonView('setOption', optionList);

	});

	$('.buttonView.withdrawChallengeButton').each(function(index)
	{
		/*
		 * Sets up withdrawChallengeButton and acceptChallengeButton
		 */
		var button = $(this);
		var buttonData = button.data('data');
		var optionList = {
			action : function()
			{
				$.callAjax({
					url : buttonData.postUrl,
					data : {
						id : buttonData.gameId
					},
					success : function()
					{
						window.location.reload();
					}
				});
			}
		};
		button.buttonView('setOption', optionList);

	});
}