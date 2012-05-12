$(document).ready(function()
{
	/*
	 * weekView template plugin
	 * Sets the mousevents & popups for weekView event days
	 */
	(function()
	{
		var methodList = {
			init : function(selector, optionList)
			{
				optionList = $.extend({
					eventNameSpace : 'weekView',
					position : {
						my : 'left top',
						at : 'left bottom'
					},
					dialog : {
						minHeight : '100'
					}
				}, optionList);

				//returns the dialog data of the eventDay whose event is triggered
				var getDialogData = function()
				{
					var eventDayData = $(this).data('data');
					var dialogId = optionList.eventNameSpace + '_' + eventDayData.id;
					var dialogTitle = eventDayData.dayName + (null != eventDayData.date ? (', ' + eventDayData.date) : "");
					var dialogContent = function(popupCallback)
					{
						$.callAjax({
							dataType : 'html',
							url : eventDayData.url,
							data : {
								eventList : eventDayData.eventList
							},
							success : function(data, textStatus, jqXHR)
							{
								popupCallback(data);
							}
						});
					};
					return {
						id : dialogId,
						title : dialogTitle,
						content : dialogContent
					};
				};

				optionList.getDialogData = getDialogData;
				return this.ybPopup(optionList, selector);
			},

			/*
			 * Displays the empty schedule alert 
			 */
			alertEmptySchedule : function(optionList)
			{
				var dialogElement = $('<div>').addClass('dialog').addClass('alertEmptySchedule').append(optionList.content);
				dialogElement.callDialog($.extend({
					height : this.parent().height() - this.height() - 10, // 10 for padding etc.
					width : this.parent().width() - 6,// "6" is related to css border width or smth similar
					modal : false,
					draggable : false,
					resizable : false
				}, optionList));

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
			weekView : function()
			{
				return this.invokePlugin.apply(this, [ methodList, arguments ]);
			}
		});
	})();
});