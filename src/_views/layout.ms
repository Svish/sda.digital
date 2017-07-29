{{% BLOCKS }}
<!DOCTYPE html>
<html lang="{{LANG}}">
	<head>
		<title>{{$title}}{{/title}} - SDA.digital</title>

		<base href="{{#_url}}/{{/_url}}" />
		<link rel="canonical" href="{{#_url}}/{{_path}}{{/_url}}">

		{{! TODO: >favicons}}

		<meta name="viewport" content="width=device-width, initial-scale=1">
		{{#_css}}
		<link rel="stylesheet" type="text/css" href="{{.}}">
		{{/_css}}

		{{#_js}}
		<script type="text/javascript" src="{{.}}"></script>
		{{/_js}}
		<script>
			var Site = {
					Env: '{{ENV}}',
					Url: {
						Base: '{{_url}}',
						Current: '{{_path}}'
					}
				};
		</script>

	</head>
	<body class="{{_pc}}">
		{{> header}}

		{{{messages}}}

		<div id="content">
			{{$content}}{{/content}}
		</div>

		{{> footer}}


		{{{clicky}}}
		
	</body>
</html>
