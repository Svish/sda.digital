
{{!TODO: Check if canonical is used when PATH is not same}}
{{#site_id}}
<script type="text/javascript">

	{{#user}}
	var clicky_custom = {
		visitor: {username: 'User({{id}})'}
	};
	{{/user}}

    var clicky_site_ids = [{{site_id}}];

    (function() {
        var s = document.createElement('script');
        s.type = 'text/javascript';
        s.async = true;
        s.src = '//static.getclicky.com/js';
        ( document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0] ).appendChild( s );
    })();
</script>
{{/site_id}}
