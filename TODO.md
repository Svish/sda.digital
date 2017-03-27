Up next
===

- (!) Fetch messages via ajax
	- Remove after showing (currently invisibly hides menu...)

- (!) Change to model->for_x(X $type)
	- $type->id()
	- public function id() this->$auto_inc()

- Edit content
- Edit files
	
- Add session_id() to fresh_log table?
- (!) Merge list-items on item pages somehow?
	- Order by "label"


Maps
===
- Make cached controller instead of hardcoded map.mustache
	- https://developers.google.com/maps/documentation/static-maps/intro#quick_example
- (!) Implement Signing
	- https://developers.google.com/maps/documentation/static-maps/get-api-key#sample-code-for-url-signing


Tidbits
===

- (!) Use Knockout on user/me? And contact? Others?

- Move array_* from functions.inc to Array class

- Deal with multiple messages covering each other
	- Get messages via ajax? (admin/users)
	
- Knockout model extend/inherit/something
	- Look into js loader stuff?

- Flash updated fields after ID3-search

- When clicking person in location
	- highlight content at that location on person page
	- similar other places?

- Rename 'speakers' to 'persons' in list views and queries

- Rename '.mustache' => '.m'?


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
		- Option:
			- Put single (and replace with content template)
			- Add as series (including already put)
		- Auto-complete place and speakers
	
	- Update info

	- Add remote content?
		- YouTube?
		- Vimeo?
		- Facebook?
		- Etc?
		- Use Redded 2016 for testing?
	- List lost content (shouldn't get any, but who knows...)


- Persons/Places
	- (!) Throw ValidationFailed? if delete fails because of constraint
	- Show count of content
	- Link to person/place page
		- person/<id>/<slug>



Public
===

- Cache model::for_(speaker/location/series/content) view data
	- (!) Remember footer and header changes...
	- Somehow clear on save
		- Don't remove slashes in cached key names, then delete 'url'?

- Front
	- Remove "Siste - " from <title>
	- Latest content
		- By added
		- Fetch more link/button
	- Searchbar on top
		- Search for both content and series?
		- Result replaces latest content
	- http://flexbox.help/


- (!) Show content length AND speaker
	- How to get smoothly in person/series/location listings?

- Locations
	- Link map to google maps page

- Series
	- Playlist-player?
	- Reorder
	- Remove content
	- Add content
		- Emphazis on fresh-log

- Content
	- List files
	- Embed codes?
	- Share widget?
	- Edit
		- Content editable?
		- {{#role.editor}}
		- Add files (select from new)
		- Add to / Remove from series
	- https://www.audioverse.org/english/sermons/recordings/3162/revival-and-transformation.html
	- https://www.audioverse.org/english/sermons/recordings/13863/1-soul-searching-our-emotions.html

- File
	- Generate name from content
		- "Speaker - Title (time, etc.?)"

- RSS Feed
	- Latest content (created)
	- Latest sermon (recorded)
	- Series
	- Person?

- Search?
	- In titles and descriptions?
	- Include speakers and series?
		- Like iMDB: Speakers, Series, Content (hide empty sections)

- Time?
	- List by year/month
	- Choose between created/time
		- (!) Rename time => recorded

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
