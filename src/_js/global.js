
// Hook up global ajax listeners
$(function()
{
	// Global ajax progress
	$(document)
		.ajaxStart(NProgress.start)
		.ajaxStop(NProgress.done)
		.ajaxError(errorHandler);
});


function errorHandler(event, jqxhr, settings, thrownError)
{
	var body = /<body.*>([\s\S]+)<\/body>/
		.exec(jqxhr.responseText);

	if( ! body)
		return;

	var html = $('<output>')
		.append($.parseHTML(body[1]))
		.find('#message,#content');

	$('#content')
		.replaceWith(html);
}


function confirmedReset()
{
	return confirm('Sikker? 🤔');
}

/**
 * PolyFill: String.contains
 * @see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/includes#Polyfill
 */
String.prototype.includes = String.prototype.includes || function(search, start)
{
	if (typeof start !== 'number')
		start = 0;

	if (start + search.length > this.length)
		return false;
	else
		return this.indexOf(search, start) !== -1;
	
};



/**
 * PolyFill: Number.isInteger
 * @see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Number/isInteger#Polyfill
 */
Number.isInteger = Number.isInteger || function(value)
{
	return typeof value === 'number' && 
		isFinite(value) && 
		Math.floor(value) === value;
};
