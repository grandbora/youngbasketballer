$(document).ready(function()
{
	/*
	 * connectionListView template plugin 
	 * Sets the mousevents & popups for connectionListView
	 */
	(function()
	{
		var methodList = {

			init : function(selector, optionList)
			{
				optionList = $.extend({
					eventNameSpace : 'connectionListView',
					dialog : {
						width : 230
					},
					position : {
						my : 'left top',
						at : 'right top'
					}
				}, optionList);

				var getDialogData = function()
				{
					var connectionData = $(this).data('data');
					var dialogId = optionList.eventNameSpace + '_' + connectionData.id;
					var refresh = false;

					if (true === connectionData.refresh) {
						refresh = true;
						connectionData.refresh = null;
						$(this).data('data', connectionData);
					}

					return {
						id : dialogId,
						title : connectionData.title,
						content : connectionData.content,
						refresh : refresh
					};
				};

				optionList.getDialogData = getDialogData;
				return this.ybPopup(optionList, selector);
			},

			/*
			 * Displays the empty schedule alert 
			 */
			alertEmptyConnectionList : function(optionList)
			{
				var dialogElement = $('<div>').addClass('dialog').addClass('alertEmptyConnectionList').append(optionList.content);
				dialogElement.callDialog({
					modal : false,
					draggable : false,
					resizable : false,
					height : this.parent().height() - this.height() - 10, // 10 for padding etc.
					width : this.parent().width() - 6, // "6" is related to css border width or smth similar
					title : optionList.title
				});
				// position dialog's parent(container)
				dialogElement.parent().position({
					of : this,
					my : 'left top',
					at : 'left bottom',
					offset : "0 0",
					collision : "none"
				});
				return this;
			}
		};

		$.fn.extend({
			connectionListView : function()
			{
				return this.invokePlugin.apply(this, [ methodList, arguments ]);
			}
		});
	})();
});

/**
 * Sets up the buttons for connectionList
 */
function setUpConnectionListButtons()
{
	var commonOptionList = {
		prepareDialog : function(dialogElement)
		{
			var container = this.parents('.connectionListView').first().clone();
			container.find('.button,script,tr:not(:first):not(#' + this.parent().parent().prop('id') + ')').remove();
			dialogElement.find('.connection').empty().append(container);
		}
	};

	/*
	 * Sets up removeConnectionButton
	 */
	$('.connectionListView .removeConnectionButton').each(function(index)
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
									if (2 > currentConnectionRow.siblings('tr').length) {
										// no connection left in the view
										var connectionData = currentConnectionRow.data('data');
										var noDataRow = $('<tr>').addClass('noData').addClass('helperLine').addClass('connection').hide().append($('<td>').html(connectionData.noDataText));
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
	$('.connectionListView .createConnectionButton').each(function(index)
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
								id : buttonData.targetUserId
							},
							success : function()
							{
								var currentConnectionRow = button.parent().parent();
								var connectionData = currentConnectionRow.data('data');
								connectionData.content += '<div class="attention">You have dismissed this player.</div>';
								connectionData.refresh = true;
								currentConnectionRow.data('data', connectionData);
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

	/*
	 * Sets up modifyConnectionButton
	 */
	$('.connectionListView .modifyConnectionButton').each(function(index)
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
								var connectionData = currentConnectionRow.data('data');
								connectionData.content += '<div class="attention">You have accepted this offer.</div>';
								connectionData.refresh = true;
								currentConnectionRow.data('data', connectionData);
								button.parent().removeClass('loader');
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