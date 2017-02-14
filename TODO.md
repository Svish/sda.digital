
- (!) Use Knockout on user/me? And others
?
- Deal with multiple messages covering each other



RelationalSql extends Sql
===

- Add() / Remove()
	- Add dirty rel for save
- Save()
- Load with()
	- Group by?
	- key:foo?
	- stream with yield and manual pdo "fetcher"?
	- Query()->stream(...$with)
		- implode(', ', map($rel_columns, (c) => "$rel.$c AS :$rel:$c"))
			- Or just use $self.$c and $rel.$c?
		- Load/Handle relationships?
			- If class is RelationalSql


Admin
===

- Content
	- Add fresh
		- Get info via id3
		- Info editable
		- Option:
			- Put single (and replace with content template)
			- Add as series (including already put)
		- Auto-complete place and speakers

		- Submit
			- begin transaction
			- insert rows
			- copy files
				- ha/sh/hash_id.ext
			- commit transaction
			- if good, delete originals, otherwise delete copies


	- Reset to new?
	- Update info
	- Browsing editable?

	- Add remote content?
		- YouTube?
		- Vimeo?
		- Facebook?
		- Etc?
		- Use Redded 2016 for testing?
	- List lost content (shouldn't get any, but who knows...)

- Series
	- Delete
		- Remove sermons?
		- Make sermons without serie?
	- Reorder
	- Remove content

- Speakers/Places
	- Reset to new?
	- Update info
	- Editable browsing?
	- Restrict delete if attached to content


Profile?
===

- Add timestamps
	- Created timestamp
	- Verified timestamp
		- Set on email verification
		- Delete accounts not verified? (where !verified and created vs now())
- Add last_login / login_log?
- MyFavorites? (favorite_series_id =>)
- MySeries? (series.owner != null)



Public
===

- Cache views
	- Somehow clear on save
		- Don't remove slashes in cached key names, then delete 'url'?
- (!) Set canonical on slug pages
	- Overrideable _path in Template ($context + [_path => PATH])

- Front
	- Latest content
		- By added
	- Searchbar on top
		- Result replaces latest content
	- http://flexbox.help/

- Speakers/Series/Places
	- Browse lists
	- Content editable?

- Speakers
	- List series partaking in
	- Then content (or somehow side-by-side?)

- Content
	- List files
	- Link to speaker/series/place
	- List series this is in at bottom
		- Only owner == null? Or on top?
	- Embed codes?
	- Share widget?
	- Edit
		- Content editable?
		- {{#role.editor}}
		- Add files (select from new)
		- Add to / Remove from series

- RSS Feed
	- Latest uploads
	- Latest sermon

- Search?
	- In titles and descriptions?
	- Include speakers and series?
		- Like iMDB: Speakers, Series, Content (hide empty sections)

