/**
 * Sets up the buttons for requestList
 */
function setUpRequestListButtons()
{
	var commonOptionList = {
		prepareDialog : function(dialogElement)
		{
			var container = this.parents('.requestListView').first().clone();
			container.find('.button,script,tr:not(#' + this.parent().parent().prop('id') + ')').remove();
			dialogElement.find('.connection').empty().append(container);
		}
	};

	/*
	 * Sets up removeConnectionButton
	 */
	$('.requestListView .removeConnectionButton').each(function(index)
	{
		var button = $(this);
		var buttonData = button.data('data');

		var optionList = $.extend(commonOptionList, {
			dialogOptionList : {
				buttons : [ {
					text : buttonData.title,
					click : function()
					{
						$.callAjax({
							url : buttonData.postUrl,
							data : {
								id : buttonData.connectionId
							},
							success : function()
							{
								var currentConnectionRow = button.parent().parent();
								currentConnectionRow.hide(1000);
								var resetList = function()
								{
									if (1 > currentConnectionRow.siblings('tr').length) {
										// no request left in the view
										var connectionData = currentConnectionRow.data('data');
										var noDataRow = $('<tr>').addClass('noData').addClass('helperLine').addClass('request').hide().append($('<td>').html(connectionData.noDataText));
										currentConnectionRow.parent().empty().append(noDataRow);
										noDataRow.show(1000, function()
										{
											noDataRow.css('display', '');
										});
									} else {
										currentConnectionRow.remove();
									}
								};
								var timer = window.setTimeout(resetList, 900);
							},
							error : function(data, textStatus, jqXHR)
							{
								window.location.reload();
							}
						});
						$(this).dialog("close");
						button.parent().addClass('loader');
						button.parent().parent().find('.button').hide();
					}
				} ]
			}
		});

		button.buttonView('setOption', optionList);
	});

	/*
	 * Sets up createConnectionButton
	 */
	$('.requestListView .createConnectionButton').each(function(index)
	{
		var button = $(this);
		var buttonData = button.data('data');

		var optionList = $.extend(commonOptionList, {
			dialogOptionList : {
				buttons : [ {
					text : buttonData.title,
					click : function()
					{
						daylist = [];
						$(this).find('.dayList input:checked').each(function(index)
						{
							daylist.push($(this).val());
						});
						salary = $(this).find('input[name=salary]').val();

						$.callAjax({
							url : buttonData.postUrl,
							data : {
								id : buttonData.targetUserId,
								daylist : daylist,
								salary : salary
							},
							success : function()
							{
								var currentConnectionRow = button.parent().parent();
								var connectionData = {
									content : '<div class="attention">You have sent an offer to this player.</div>',
									id : 'info_' + currentConnectionRow.attr('id')
								};
								currentConnectionRow.data('data', connectionData);

								currentConnectionRow.informationIndicator({
									eventNameSpace : 'acceptedTransferRequest_' + currentConnectionRow.attr('id'),
									dialog : {
										width : 205,
										minHeight : 25.6
									},
									position : {
										my : 'left top',
										at : 'right top'
									}
								});
								button.parent().removeClass('loader');
							},
							error : function(data, textStatus, jqXHR)
							{
								window.location.reload();
							}
						});
						$(this).dialog("close");
						button.parent().addClass('loader');
						button.parent().parent().find('.button').hide();
					}
				} ]
			}
		});

		button.buttonView('setOption', optionList);
	});
}