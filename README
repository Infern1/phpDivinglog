
phpDivingLog 2.0b12

Changes v2.0a1 - 2007-09-21 - Rob Lensen
    * Changed phpdivinglog to use smarty templates
    * Before installing check the file config.inc.php
        - Set the database settings
        - Set $_config['web_root']
        - Set $_config['abs_url_path']
        - Set $_config['app_root']
    * Added multiuser option
    * Make phpDivinglog OO
    
2006-08-25 - Lloyd Borrett (http://www.borrett.id.au)

Changes v1.8 - 2006-08-25 - Lloyd Borrett (http://www.borrett.id.au)
	Changes to the dive profile graph:
	* Added average depth to the dive profile chart. 
	* Added descent and work warnings to the dive profile chart. 
	* Better support for metric and imperial units in the dive profile chart. 
	* Provided a default dive profile chart option that displays 
          well when there is no background image. 
	Improved the readme.txt file to add extra details about 
	installation and usage.

Changes v1.7 - 2006-08-21 - Lloyd Borrett (http://www.borrett.id.au)
	Changed to support length, pressure, weight, temperature and weight
	values being able to be displayed with imperial units of measurement.
	Added support for the certification scans in divestats.php.
	Added divesummary.php to allow the display of some summary details
	on a standard .htm page.
	Improved the handling of situations where there are no site, equipment etc. details.
	Improved the display of more null values.
	Cleaned up the HTML output.
	Language file compare tool added.
	Improved the readme.txt file to add extra details about 
	installation and usage.

Changes v1.6 - 2006-08-04 - Lloyd Borrett (http://www.borrett.id.au)
	Sven Knoch released a minor update to Diving Log to fix a few bugs
	in the MySQL Dump export option - (http://www.divinglog.de)
	phpDivingLog has been updated to make use of these fixes.
	phpDivingLog 1.6 WILL NOT WORK without the updated version of Diving Log.
	Dive entry time is now displayed.
	Dive site latitude and longitude values are now properly formatted.
	Added link to Google Maps for dive site if we have the latitude and longitude.
	Fixed bug in phpDivingLog to display correct dive site country.
	Fixed bug where a dive site might have no matching country or city.
	Added additional checking of argument values to prevent 
	potential security problem.
	Improved the display of some null values.
	Improved the readme.txt file to add extra details about 
	installation and usage.

Changes v1.5 - 2006-07-26 - Lloyd Borrett (http://www.borrett.id.au)
	German language file added by Sven Knoch - (http://www.divinglog.de)
	Improved support for other languages.
	JpGraph config file includes/jpgraph/src/jpg-config.inc changed
	to have the values for a Linux system as the default. The files
	jpg-config-linux.inc and jpg-config-windows.inc are examples of 
	this file that worked on those platforms.
	Improved the readme.txt file to add extra details about 
	installation and usage.
	Changed divestats.php to include a link to the phpDivingLog page at
	http://www.borrett.id.au/interests/phpdivinglog.htm.
	Cleaned up some code.

Changes v1.4 - 2006-07-13 - Lloyd Borrett (http://www.borrett.id.au)
	Danish language file added by Henrik Absalon - (http://www.absalon.org)
	Added support for equipment listing, details and equipment photos.
	Added support for location map images.
	Added in a partial workaround for coordinate format problems.
	Fixed links to dives at a given location.
	Now showing no dive entry time instead of the bad value
	that is passed by MySQL Dump.
	Cleaned up some code.

Changes v1.3 - 2006-07-12 - Lloyd Borrett (http://www.borrett.id.au)
	Added support for dive pictures.
	Added links to the other sections on each page.
	Dive profile now shows depth scales for metres and feet.
	Added link titles to links that didn't have them.
	Moved CSS values into a CSS file.
	Moved configuration values into a config file.
	Moved language values into a languages file.
	Cleaned up some code .

Changes v1.2 - 2006-07-09 - Lloyd Borrett (http://www.borrett.id.au)
	Added Diving Certifications.
	Separated statistics into divestats.php.
	Show program revisions on the statistics page.
	Added Dive Sites as divesite.php.

Changes v1.1 - 2006-07-07 - Lloyd Borrett (http://www.borrett.id.au)
	Support for Diving Log 4.0.
	Conversion to English.
	Added in navigation links and extra details.

Original version 1.0 created by Olaf van Zandwijk (http://enschede.vanzandwijk.net)


EXAMPLE WEB SITE:
	http://www.borrett.id.au/divelog/


DEPENDENCIES:
	JpGraph 1.20.4a (http://www.aditus.nu/jpgraph/)

	RTFClass (http://www.phpclasses.org/browse/file/7632.html)
	Rich Text Format - Parsing Class - (c) 2000 Markus Fischer

	Lightbox JS v2.0 (http://www.huddletogether.com/projects/lightbox2/)
	Used to overlay dive pictures on the current page.


INSTALLATION:

	Sorry, there is no installer. It is a manual process.


	THE BASICS

	Do you have FTP software? You will need a decent FTP software
	package to transfer files back and forth from your computer.

	Do you have a good Text Editor? You will nedd one to modify
	some of the phpDivingLog files during installation, plus the 
	MySQL Dump file you load into your web MySQL database.

	Do you have access to your web host Control Panel, or some 
	other way to create a MySQL database and user? If not, you
	may have to ask tour web host to do that for you.

	Do you have access to phpMyAdmin, or some other way to
	load data into a MySQL database?


	BEGIN INSTALLATION

1.	Create a MySQL database on your server, e.g. YOURNAME_divelog.
	Create or assign a MySQL database username and password to the database
	with ALL privileges.

2.	Edit the file includes/config.inc.php and replace the values for...
// database information
$_config['database_server'] = "localhost";
$_config['database_db'] = "your_database";
$_config['database_username'] = "your_username";
$_config['database_password'] = "your_passwordj";
	with the appropriate values for your database.

3.	If you are planning to use a language file other than
	includes/languages/english.inc.php, then you will
	also need to specify the language in the configuration file
	includes/config.inc.php by changing the value for...
// language setting determines which language file is used
$_config['language'] = "english";

4.	If you wish to have values display with imperial units values
	instead of metric ones, you will need to edit the 'Unit Conversion'
	values in the includes/config.inc.php file.
	Set values to true if you want to convert from metric units 
	to imperial units, or to false if values should be left as metric units. 

5.	Edit the files includes/header.tpl and includes/footer.tpl,
	includes/footerequip.tpl, includes/footersites.tpl and
	includes/footerstats.tpl to have the HTML code needed 
	for your web site.

	You will need to ensure the following is somewhere in your 
	includes/header.tpl file...
 <link rel="stylesheet" type="text/css" media="screen"
  href="includes/divelog.css">

6.	You can change the styles used to display values by
	editing the CSS file includes/divelog.css.

7.	You may need to edit the file 
	includes/jpgraph/src/jpg-config.inc
	to set the cache and font directory values. 

	The files includes/jpgraph/src/jpg-config.inc and 
        includes/jpgraph/src/jpg-config-linux.inc already
	have the values used sucessfully on Linux based systems.
DEFINE("CACHE_DIR","/tmp/jpgraph_cache/");
DEFINE("TTF_DIR","includes/jpgraph/fonts/");
DEFINE("MBTTF_DIR","includes/jpgraph/fonts/");

	The file includes/jpgraph/src/jpg-config-windows.inc 
	has the values used on a Windows platform.

8.	Upload, via FTP, all of the files into a directory on 
	your server. Example: /divelog.

	Each web host has his/her own preference in naming 
	folders for use in running a website.
	You can have many files that don't even get shown to 
	the public. The ones that are available for access via 
	a browser are usually in a folder called something like: 

	- /home/YOURNAME/public_html
	or
	- /var/www/YOURNAME/httpdocs
	or
	- /usr/accounts/a/b/YOURNAME/httpd
	etc, etc, etc

	If it's unclear where the publicly-accessible files are 
	to be uploaded, talk to your webhost for assistance.

	Typically on a Linux based server you would make sure
	you set permissions on your directories to 755 and files
	to 644 or 444, depending on your web server configuration.
	The Linux commands to do this are:
	  find divelog/ -type f -exec chmod 644 {} \;
	  find divelog/ -type d -exec chmod 755 {} \;

9.	Do a MySQL Dump from Diving Log 4.0.5
	(http://www.divinglog.de/english/home/index.php)
	On the 'General' tab...
	  Logbook: Select all of your dives.
	  Format: Select 'Structure and Data' and select "With 'DROP TABLE'".
	  Tables: Select all tables.
	On the 'Additional' tab...
	  Typically you would just select 
	    'Remove all pathnames (export only filenames)'
	Click n 'Start Export'

10.	Edit the dump file produced as required to change
	the pathnames to images, if yu were unable to achieve 
	what you needed using the options on the 'Additonal' tab.
	(See USAGE below for details.)

11.	Use phpMyAdmin or similar to upload the data into your 
	MySQL database.


USAGE:
	GOOGLE MAPS
	Version 1.2+ supports a user provided link to Google Maps.

	If you add a line like:
		[url]http://maps.google.com/?ie=UTF8&t=k&om=1&ll=-38.356776,144.772693&spn=0.005645,0.007124[/url]
	in the Dive Location Details Comments, it will get 
	turned into a link to the specified Google Map.


	DIVE PICTURES
	Version 1.3+ supports dive pictures.

	For ease of use, all dive pictures should be imported 
	into diving log from the same directory,
	e.g. E:\divelog\images\pictures.

	(To make it easy to know which picture is which, I also
	use filenames like 27_1.jpg, 27_2.jpg, 23_1.jpg,
	being the first and second pictures for dive number 27, 
	and the first picture for dive number 23. But all that 
	is required is that each picture filename be unique.)

	Set the path to your pictures on your web site
	in the file locations section of includes/config.inc.php,
	for example...
$_config['picpath_web'] = "images/pictures/";

	Upload your new pictures to the directory on your web server.

	In version of Diving Log prior to version 4.0.5,
	after you do your MySQL Dump from Diving Log, you would
	need to edit the .sql file produced to strip out the path 
	from the picture filenames. For example,
	your full picture pathnames might be,
	"E:\divelog\images\pictures\27_1.jpg".
	Search for "E:\divelog\images\pictures\"
	and replace it with "", leaving just "27_1.jpg".

	However, with the changes to the Diving Log MySQL Dump
	option in version 4.0.5, you should no longer need to
	do this editing.

	Save the edited MySQL Dump file and upload it into 
	your web database.

	For the dive pictures to work with Lightbox JS v2
	the following has to be somewhere in your 
	includes/header.tpl file...
 <script type="text/javascript" 
  src="includes/lightbox/prototype.js"></script>
 <script type="text/javascript" 
  src="includes/lightbox/scriptaculous.js?load=effects"></script>
 <script type="text/javascript" 
  src="includes/lightbox/lightbox.js"></script>
 <link rel="stylesheet" type="text/css" media="screen"
  href="includes/lightbox/lightbox.css">


	EQUIPMENT PHOTOS
	Version 1.4+ supports equipment photos.
	Use the same guidelines as for the dive pictures. 


	MAP IMAGES
	Version 1.4+ supports map images.
	Use the same guidelines as for the dive pictures. 


	CERTIFICATION SCAN IMAGES
	Version 1.7+ supports map images.
	Use the same guidelines as for the dive pictures. 


	METRIC & IMPERIAL UNITS
	Version 1.7+ supports display units as either metric or imperial.

	Diving Log stores all depth, pressure, weight, temperature 
	and volume values in metric units. Thus metric units are 
	exported via MySQL Dump and imported into your online
	MySQL database.

	However, like Diving Log, phpDivingLog gives you the option
	to display values using either metric or imperial units.

	If you wish to have values display with imperial unit values, 
	you will need to edit the 'Unit Conversion' values in the 
	includes/config.inc.php file.

	Set the unit configuration values to 'true' if you want to convert
	values and have them display as imperial units, or to 'false' if 
	values should be displayed as metric units. 


	DIVE SUMMARY

	divesummary.php is a PHP script that can be called 
	from a standard HTML page to show some dive log 
	summary details in that web page, plus links to phpDivingLog.

	To invoke it on a Linux / Apache based server,
	add the following line to your .htaccess file:
		AddHandler server-parsed .htm

	Then, in the HTML file you want the output from
	divesummary.php to appear in, place the following line 
	where you want the output to go:
		<!--#include virtual="../divelog/divesummary.php" -->


	LANGUAGE COMPARE SCRIPT

	A language file comparison PHP script has been sourced and added.
	Those of you either maintaining or building new language file
	for phpDivingLog will find this script useful.

	The script is includes/languages/compare.php. It needs to be 
	where the language files are. Call it from the browser: 
	http://example.com/divelog/includes/language/compare.php?f=danish 
	to check "danish.inc.php" against "english.inc.php".


	DIVE PROFILE GRAPH

	Version 1.7+ supports the display of a dive profile graph
	without a background image. You can choose to provide a 
	suitable 500 x 400 background image file (gif or jpg) and 
	specify the path to it in the configuration file 
	'includes/config.inc.php'.

	By default, phpDivingLog will use the setting for the length 
	units in the configuration file to determine if metres or 
	feet will be used in the dive profile graph. It will show 
	the primary units as the left side Y scale, and the 
	secondary units as the right side Y scale.

	Via seetings in the configuration file 'includes/config.inc.php',
	you can choose to show only the left Y scale, both the left
	and right Y scales with the same primary units, or with
	the primary units as the left side Y scale, and the 
	secondary units as the right side Y scale (the default
	setting).

	WARNING: If you choose to use a background image, it is
	likely that the colour settings specified in 'drawprofile.php'
	may not be ideal. Your solution is to edit these colour
	settings to produce a set that you like, which works well
	with your background image.


# END #
