<footer id="footer" class="no-select">
	<nav role="navigation">
		<span>Copyright © <a href="{{_url}}">SDA.digital</a></span>
		<span><a href="about">Om nettsiden</a></span>
		<span><a href="contact">Kontakt oss</a></span>


		{{^_user}}
		<span><a href="user/login?url={{PATH|urlencode}}">Logg inn</a></span>
		{{/_user}}

		{{#_user}}
		<span><a href="user/logout">Logg ut</a></span>
		<span><a href="user/me">{{email}}</a></span>
		{{/_user}}

	</nav>
</footer>
