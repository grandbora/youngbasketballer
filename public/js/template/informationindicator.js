$(document).ready(function()
{
	/*
	 * informationIndicator
	 * Small plugin to initialize informationIndicators
	 * 
	 */
	(function()
	{
		var methodList = {
			init : function(optionList)
			{
				optionList = $.extend(true, {
					eventNameSpace : 'informationIndicator',
					dialog : {
						hideTitle : true,
						width : 350,
						minHeight : 20
					},
					position : {
						my : 'left bottom',
						at : 'right top',
						collision : 'fit flip'
					}
				}, optionList);

				var getDialogData = function()
				{
					informationData = $(this).data('data');
					return {
						id : informationData.id,
						content : informationData.content
					};
				};

				optionList.getDialogData = getDialogData;
				return this.ybPopup(optionList);
			}
		};

		$.fn.extend({
			informationIndicator : function()
			{
				return this.invokePlugin.apply(this, [ methodList, arguments ]);
			}
		});
	})();
});