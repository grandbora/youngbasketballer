$(document).ready(function()
{
	//jQuery Extensions
	$.extend({
		//plugin invoker method, for extending jQuery Object
		invokePlugin : function(methodList, methodArgumentList)
		{
			if (0 < methodArgumentList.length && null != methodList[methodArgumentList[0]]) {
				context = this;
				sliceCount = 1;
				if (1 < methodArgumentList.length && "object" === typeof methodArgumentList[1]) {
					context = methodArgumentList[1];
					sliceCount = 2;
				}
				methodList[methodArgumentList[0]].apply(context, Array.prototype.slice.call(methodArgumentList, sliceCount));
				return this;
			} else {
				return methodList.init.apply(this, methodArgumentList);
			}
		}
	});

	//jQuery prototype extensions, jQuery.fn is pronounced jQuery effin'
	$.fn.extend({
		//plugin invoker method, for extending jQuery prototype
		invokePlugin : function(methodList, methodArgumentList)
		{
			if (0 < methodArgumentList.length && null != methodList[methodArgumentList[0]]) {
				return methodList[methodArgumentList[0]].apply(this, Array.prototype.slice.call(methodArgumentList, 1));
			} else {
				return methodList.init.apply(this, methodArgumentList);
			}
		}
	});

	$.extend({
		//wrapper for $.ajax()
		// @todo dont wrap, extend the original ajax
		callAjax : function(optionList)
		{
			var originalSuccessHandler = optionList.success;
			optionList = $.extend({
				type : 'POST',
				dataType : 'json',
				error : function(data, textStatus, jqXHR)
				{
					// @todo implement an retry limit
					// retry with time out
					// $.callAjax(optionList);
					// $.error('error in ajax, retrying...');
				}
			}, optionList, {
				success : function(data, textStatus, jqXHR)
				{
					if ('function' === typeof originalSuccessHandler) {
						originalSuccessHandler(data, textStatus, jqXHR);
					}

					$(document).trigger('ajaxCompleted.yb');
				}
			});
			$.ajax(optionList);
		}
	});

	//jQuery prototype extensions, jQuery.fn is pronounced jQuery effin'
	$.fn.extend({
		// wrapper for $.dialog()
		// @todo dont wrap, extend the original dialog
		callDialog : function(optionList)
		{
			optionList = $.extend({
				hideTitle : false,
				preventClose : false,
				modal : true,
				minHeight : 50,
				position : 'center center'
			}, optionList);

			this.addClass('dialog');

			// setup hideTitle property
			if (true === optionList.hideTitle) {
				// puzzle of the day (A:adds class to dialogClass)
				var hideTitleCssClass = ' hideTitle';
				if (!optionList.dialogClass || !(optionList.dialogClass += hideTitleCssClass))
					optionList.dialogClass = hideTitleCssClass;

				optionList.preventClose = false;
			}

			// setup preventClose property
			//@todo extend plugin, add getter setter for preventClose
			if (true === optionList.preventClose) {

				// puzzle of the day (A:adds class to dialogClass) 
				var preventCloseCssClass = ' preventClose';
				if (!optionList.dialogClass || !(optionList.dialogClass += preventCloseCssClass))
					optionList.dialogClass = preventCloseCssClass;

				optionList.closeOnEscape = false;
				optionList.modal = true;

				$(document).off('click', '.ui-widget-overlay');
			} else {
				//@todo this event is binded multiple time, prevent it
				_this = this;
				$(document).on('click', '.ui-widget-overlay', function()
				{
					_this.dialog('close');
				});
			}

			return this.dialog(optionList);
		}
	});

	/*
	 * watermark plugin http://www.weblap.ro/res/tinywatermark/demo.php edited a lot from original, 
	 * support for password type removed currently works by object replacement, if this causes any
	 * trouble switch to classname replacement method explained on demo page
	 * 
	 * this plugin uses this.selector therefore must be called on objects created by selectors
	 * @todo fix that this.selector (get rid of it)
	 */
	(function()
	{
		$.fn.extend({
			watermark : function(optionList)
			{
				optionList = $.extend({
					classNameToAppend : 'watermark'
				}, optionList);

				var e = function(e)
				{
					var i = $(this);
					if (!i.val()) {
						var w = i.attr('title');
						var $c = $($("<div />").append(i.clone()).html()).val(w).addClass(optionList.classNameToAppend);
						i.replaceWith($c);
						$c.focus(function()
						{
							$c.replaceWith(i);
							setTimeout(function()
							{
								i.focus();
							}, 1);
						}).change(function(e)
						{
							i.val($c.val());
							$c.val(w);
							i.val() && $c.replaceWith(i);
						}).closest('form').submit(function()
						{
							$c.replaceWith(i);
						});
					}
				};
				$(document).on('blur.watermark change.watermark', this.selector, e);
				this.change();
			}
		});
	})();

	/*
	 * ybPopup
	 * HoverIntent Popup plugin 
	 * @author : Bora Tunca <bora.tunca@yahoo.com>
	 * 
	 * Creates jquery dialog with hover intent at given position relative to event source element
	 * 
	 * @todo this plugin has to be called once per template, 
	 * if called more than once with same options events get funny :)fix it if you need
	 * 
	 */
	(function()
	{
		var methodList = {
			init : function(optionList, selector)
			{
				optionList = $.extend(true, {
					eventNameSpace : 'ybPopup',
					dialogContainerClassSuffix : 'Container',
					delay : 400,
					getDialogData : function()
					{
						return {
							id : "ybPopup_id",
							title : "",
							content : ""
						};
					},
					dialog : {
						modal : false,
						draggable : false,
						resizable : false,
						width : 300
					},
					position : {
						my : 'left top',
						at : 'right top',
						collision : "flip flip",
						offset : 0
					}
				}, optionList);

				var timer;
				var dialogClass = optionList.eventNameSpace + "Dialog";
				var dialogContainerClass = optionList.eventNameSpace + optionList.dialogContainerClassSuffix;

				// closes all dialogs
				var _closeAll = function()
				{
					$('.' + dialogClass).dialog('close');
				};

				// methods for setting and clearing timers
				var _clearTimer = function()
				{
					window.clearTimeout(timer);
				};
				var _setTimer = function()
				{
					timer = window.setTimeout(_closeAll, optionList.delay);
				};

				var _renderDialog = function()
				{
					var dialogData = optionList.getDialogData.apply(this);

					var dialogElement = $('div.dialog.' + dialogClass + '#' + dialogData.id);
					if (true === dialogData.refresh) {
						dialogElement.remove();
						dialogElement = $('div.dialog.' + dialogClass + '#' + dialogData.id);
					}

					if (0 === dialogElement.length) {
						dialogElement = $('<div>').addClass('dialog').addClass(dialogClass).attr('id', dialogData.id);

						if ("function" === typeof dialogData.content) {// for ajax calls
							dialogElement.addClass('loader');
							var contentCallback = function(content)
							{
								dialogElement.removeClass('loader').append(content);
							};
							dialogData.content.apply(this, [ contentCallback ]);
						} else {
							dialogElement.append(dialogData.content);
						}
					}

					_clearTimer();
					_closeAll();

					// open new one
					dialogElement.callDialog($.extend(optionList.dialog, {
						dialogClass : dialogContainerClass,
						title : dialogData.title
					}));

					// position dialog's parent(container)
					dialogElement.parent().position($.extend(optionList.position, {
						of : $(this)
					}));
				};

				// set dialog mouse events
				//@todo remove binding to document
				$(document).on('mouseleave.' + optionList.eventNameSpace, '.' + dialogContainerClass, _setTimer);
				$(document).on('mouseenter.' + optionList.eventNameSpace, '.' + dialogContainerClass, _clearTimer);

				// set target element mouse events
				if ('undefined' !== typeof selector) {
					this.on('mouseleave.' + optionList.eventNameSpace, selector, _setTimer);
					this.on('mouseenter.' + optionList.eventNameSpace, selector, _renderDialog);
				} else {
					this.on('mouseleave.' + optionList.eventNameSpace, _setTimer);
					this.on('mouseenter.' + optionList.eventNameSpace, _renderDialog);
				}

				return this;
			}
		};

		$.fn.extend({
			ybPopup : function()
			{
				return this.invokePlugin.apply(this, [ methodList, arguments ]);
			}
		});
	})();

	/*
	 * sideBarLink
	 * Small plugin to create loading effect to sidebar links 
	 */
	(function()
	{
		var methodList = {
			init : function(optionList)
			{
				optionList = $.extend(true, {}, optionList);
				var linkList = this;
				this.on('click.sideBarLink', function(event)
				{
					// clear all (if any)
					linkList.children('div.loader').remove();
					linkList.removeClass('hover');

					// append loader
					$(this).append($('<div>').addClass('loader'));
					$(this).addClass('hover');
					$(this).add('html, body').css('cursor', 'progress');
				});

				return this;
			}
		};

		$.fn.extend({
			sideBarLink : function()
			{
				return this.invokePlugin.apply(this, [ methodList, arguments ]);
			}
		});
	})();

	/*
	 * Global onload functions
	 */

	// fire up sideBar links
	$('.sideBar .linkList .link').sideBarLink();

	// fire up buttons
	$('.button').button();

	// fire up watermarked input texts
	$('input[type=text].watermarked').watermark();
});