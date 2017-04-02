Up next
===

- (!) Fetch messages via ajax
	- Remove after showing (currently invisibly hides menu...)

- (!) Merge list-items on item pages somehow?
	- Order by "label"

- (!) Rename content.time => recorded/when

- DB\Valid::must_exist
	- ['must_exist', ['table', 'column']]



Maps
===
- Make cached controller instead of hardcoded map.mustache
	- https://developers.google.com/maps/documentation/static-maps/intro#quick_example
- (!) Implement Signing
	- https://developers.google.com/maps/documentation/static-maps/get-api-key#sample-code-for-url-signing


Tidbits
===

- Use Knockout on user/me? And contact? Others?

- Move array_* from functions.inc to Array class

- Deal with multiple messages covering each other
	- Get messages via ajax? (admin/users)
	
- Knockout model extend/inherit/something
	- Look into js loader stuff?

- Flash updated fields after ID3-search

- When clicking person in location
	- highlight content at that location on person page
	- similar other places?

- Remove empty folders
	- After removing content
	- After... updating path?
	- Or just in Issues thing?



Admin
===

- Fresh content
	- Auto-complete speakers
	- Inline add to series
		- Like location, via drop-down?
		- Copy to all?	

	- Add session_id() to fresh_log table?

	- Add remote content?
		- YouTube?
		- Vimeo?
		- Facebook?
		- Etc?
		- Use Redded 2016 for testing?



Public
===

- Cache model::for_page and for_index
	- (!) Remember footer and header changes...
	- Somehow clear on save

- Front
	- Remove "Siste - " from <title>
	- Searchbar on top
		- Search for both content and series?
		- Result replaces latest content
	- Latest content
		- Fetch more link/button


- (!) Show content length AND speaker
	- How to get smoothly in person/series/location listings?


- Content
		- Add files (select from new)
		- Add to / Remove from series
	- Choose large/medium/small by comparing bitrate of same type

- File
	- Generate name from content
		- "Speaker - Title (time, etc.?)"

- RSS Feed
	- Latest content (created)
	- Latest sermon (recorded)
	- Series
	- Person?


- Search
	- In titles and descriptions?
	- Include speakers and series?
		- Like iMDB: Speakers, Series, Content (hide empty sections)
	- Full text index?
		- http://stackoverflow.com/a/11144591/39321

- Embedding and Sharing
	- Embed
		- Player for series/content/file?
	- Sharing
		- Facebook meta-data

- Sitemap.xml
	- Post to engines

- Time?
	- List by year/month
	- Choose between created/recorded



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



RelationalSql extends Sql
===

- (?) Add() / Remove()
	- Add dirty rel for save
- (?) Save()
	- Currently handled manually in models...
- (?) Load with()
	- Group by?
	- key:foo?
	- stream with yield and manual pdo "fetcher"?
	- Query()->stream(...$with)
		- implode(', ', map($rel_columns, (c) => "$rel.$c AS :$rel:$c"))
			- Or just use $self.$c and $rel.$c?
		- Load/Handle relationships?
			- If class is RelationalSql
