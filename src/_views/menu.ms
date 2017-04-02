<nav role="navigation">
	<ul id="main-menu">
		<!-- TODO: <li><a href="{{#isCurrent}}popular{{/isCurrent}}">Populært</a>-->
		<li><a href="{{#isCurrent}}person/index{{/isCurrent}}">Person</a>
		<li><a href="{{#isCurrent}}series/index{{/isCurrent}}">Serie</a>
		<li><a href="{{#isCurrent}}location/index{{/isCurrent}}">Sted</a>
		<!-- TODO: <li><a href="{{#isCurrent}}time/index{{/isCurrent}}">Tid</a>-->
		{{#_user}}
		<li><a href="{{#isCurrent}}tools{{/isCurrent}}">🛠 Verktøy</a>
		{{/_user}}
	</ul>
</nav>
