{{% ACCESS admin }}
{{% BLOCKS }}
{{< layout }}
{{$ title }}Issues{{/ title }}

{{$ content}}

<pre>// TODO

* Log to file and show it here

* Find incorrect hashes
	- Get _/files and db/files, ordered by hash, as "stream"
	- Iterate through both sets, compare and collect
		- not existing in db: orphan
		- not existing in _: missing

* Get db/files
	- Iterate and find changed files
	- Use limit to check n files each ajax request
		- Return {checked: n, changed: []}
	- Keep requesting until checked=0

</pre>

<h2>Orphans</h2>
<h2>Changed files</h2>
<h2>Missing files</h2>

{{!> waiting }}



{{/ content}}
{{/ layout }}
