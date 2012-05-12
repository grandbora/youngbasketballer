$(document).ready(function()
{
	/*
	 * buttonView
	 * Small plugin to set up buttons & properties  
	 */
	(function()
	{
		var methodList = {
			init : function(optionList)
			{
				$(this).on('click', function()
				{
					var button = $(this);
					var buttonData = button.data('data');
					if ('undefined' !== typeof buttonData.dialogId) {
						var dialogElement = $('#' + buttonData.dialogId);

						var defaultValues = {
							title : buttonData.title,
							width : 500
						};

						// prepareDialog
						buttonData.prepareDialog.apply(button, [ dialogElement ]);
						// fireup dialog
						dialogElement.callDialog($.extend(defaultValues, buttonData.dialogOptionList));
					} else {
						// execute action
						buttonData.action.apply(button);
					}
				});
				return this;
			},
			setOption : function(optionList)
			{
				var data = this.data('data');
				return this.data('data', $.extend(data, optionList));
			}
		};

		$.fn.extend({
			buttonView : function()
			{
				return this.invokePlugin.apply(this, [ methodList, arguments ]);
			}
		});
	})();

	$('.buttonView.button').buttonView();
});
