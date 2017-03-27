

/**
 * String.superTrim
 * Trims and replaces all whitespace with single spaces.
 */
String.prototype.superTrim = function()
{
	return this.trim().replace(/\s+/g, ' ');
};


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
 * PolyFill: Regexp.escape
 * @https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide/Regular_Expressions
 */
RegExp.escape = function(string)
{
  return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); // $& means the whole matched string
}



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
