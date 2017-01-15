

// Configure NProgress
NProgress.configure({
	//parent: '#content',
	showSpinner: true,
});


// End progress when everything loaded
$(window).on('load', NProgress.done);
$(function()
{
	// Start progress when DOM loaded
	NProgress.start();

	// Global ajax progress
	$(document)
		.ajaxStart(NProgress.start)
		.ajaxStop(NProgress.done);
});



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
