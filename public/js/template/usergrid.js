/**
 * Sets up the buttons for user grid
 */
function setUpUserGridButtons()
{
	$('.modelListGrid.userGrid .buttonView.createConnectionButton').each(function(index)
	{
		/*
		 * Sets up createChallengeButton
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
						var daylist = [];
						$(this).find('.dayList input:checked').each(function(index)
						{
							daylist.push($(this).val());
						});
						var salary = $(this).find('input[name=salary]').val();

						$.callAjax({
							url : buttonData.postUrl,
							data : {
								id : buttonData.targetUserId,
								daylist : daylist,
								salary : salary
							},
							success : function()
							{
								button.parent().removeClass('loader');
							}
						});
						$(this).dialog("close");
						button.parent().addClass('loader');
						button.parent().parent().find('.button').hide();
					}
				} ]
			}
		};
		button.buttonView('setOption', optionList);
	});
}