
- (!) Use Knockout on user/me?
- Deal with multiple messages covering each other

foreign key getting and setting?
	- RelationalSql extends Sql
		- __construct()
			- Figure out relations? Already done in loadTableInfo()
		- Add() / Remove()
			- Add dirty rel for save
		- Save()
			- begin transaction
			- parent::save()
			- save relationships
				- delete + insert?
			- commit transaction
	- group by?
	- key:foo?
	- stream with yield and manual pdo "fetcher"?
	- http://www.php.net/manual/en/reflectionclass.newinstancewithoutconstructor.php
	- Analyze relations to find connections 
		- define foreign keys
		- loadTableInfo()
		- one-many, many-many, many-one, many-many-through)
	- Query()->fetch*
		- Load/Handle relationships?
			- If class is


Admin
===

- User
	- Add/Remove users
		- Remove 'login'
	- Assign roles


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

