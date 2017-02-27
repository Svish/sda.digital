/**
 * Generic stuff for all pages.
 */


/**
 * Get GET parameter value
 * @see http://stackoverflow.com/a/5448595/39321
 */
function getQueryParameter(name, clean = true)
{
	var query = window.location.search.substring(1);
	var vars = query.split("&");

	for(var i in vars)
	{
		var pair = vars[i].split("=");
		if(pair[0] == name)
		{
			var value = decodeURIComponent(pair[1]);
			return clean
				? value.replace(/\+/g, ' ')
				: value;
		}
	}

	return false;
}


/**
 * Generic confirm function.
 */
function doConfirm()
{
	return confirm('Sikker? 🤔');
}


/**
 * Hook up global ajax events.
 * @see https://api.jquery.com/category/ajax/global-ajax-event-handlers/
 */
$(document)
	.ajaxStart(onAjaxStartHandler)
	.ajaxSend(onAjaxSendHandler)
	.ajaxStop(onAjaxStopHandler)
	.ajaxError(onAjaxErrorHandler);

function onAjaxStartHandler()
{
	NProgress.start();
}

function onAjaxSendHandler(e, x, opts)
{
	x.setRequestHeader('Is-Ajax', true);
}

function onAjaxStopHandler()
{
	NProgress.done();
	$('.waiting')
		.slideUp(1000); //, () => $(this).remove());
}

function onAjaxErrorHandler(event, jqxhr, settings, thrownError)
{
	if( ! jqxhr.responseJSON)
		return;

	$('#header')
		.after(jqxhr.responseJSON.message);
	
	if(jqxhr.status >= 500)
		$('#content')
			.html(jqxhr.responseJSON.reason);

}
