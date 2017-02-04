
http://www.php.net/manual/en/reflectionclass.newinstancewithoutconstructor.php

present on "working page"
	- read out id3-tags (load via ajax on request?)
	- auto-save data as json in session table?
	- optionally add to existing or new series

submit
	- begin transaction
	- insert rows
	- move files
	- commit transaction

Exceptions
===
- (!) Get error messages from text.ini
	- if($this->msg = $msg ?? Text::get(class_name))


Tools
===
- Content
	- Move to series
	- Remove from series
	- Add/Remove speakers
	- Edit
	

Admin
===

- User
	- Add/Remove users
		- Remove 'login'
	- Assign roles


- Content
	- Add new
		- Get info via id3
		- Info editable
		- Option:
			- Add single
			- Add multiple as series
		- Auto-complete place and speakers

	- Reset to new?
	- Update info
	- Browsing editable?

	- Add remote content?
		- YouTube?
		- Vimeo?
		- Facebook?
		- Etc?


- Series
	- Delete
		- Remove sermons?
		- Make sermons without serie?

- Speakers/Places
	- Reset to new?
	- Update info
	- Editable browsing?


Public
===

- Front
	- Latest uploads
		- By sermon datetime?
		- By added datetime?
		- Both? Cookie option?
	- http://flexbox.help/

- Speakers/Series/Places
	- Browse lists
	- Content editable?

- Content
	- List files
	- Link to speaker/series/place
	- Embed codes?
	- Share widget?
	- Content editable?

- RSS Feed
	- Latest uploads
	- Latest sermon

- Search?
	- In titles and descriptions?
	- Include speakers and series?
		- Like iMDB: Speakers, Series, Content (hide empty sections)



Tidbits
===

- "Move" messages to session['messages']?
	- class_exists('namespace/namespace/handlerclass')?
- Add foreign key constraints to tables
