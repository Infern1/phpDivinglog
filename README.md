# phpDivinglog

phpDivinglog is a PHP Project to display Dives entered in the Program [Divelog](http://divinglog.de/) on your own Webserver

## Getting Started

Copy config.inc.php.example to config.inc.php and changes the values according your setup. 


### Prerequisites

* A webserver, the program is tested with [Nginx](http://www.nginx.org) and [Apache](http://www.apache.org)
* [Mysql server](http://www.mysql.org)
* [PHP](http://www.php.net) at least >5.5

### Installing

Clone this project or download it, next step is to import your data into your mysql database with tools like [phpMyAdmin](https://www.phpmyadmin.net/)

Copy `config.inc.php.example` to `config.inc.php` and update the values according your setup. You should at least change your mysql database and user credentials

### Further install details

* GOOGLE MAPS Version 1.2+ supports a user provided link to Google Maps.
	If you add a line like:
[url]http://maps.google.com/?ie=UTF8&t=k&om=1&ll=-38.356776,144.772693&spn=0.005645,0.007124[/url]
	in the Dive Location Details Comments, it will get turned into a link to the specified Google Map.


* DIVE PICTURES Version 1.3+ supports dive pictures.
	For ease of use, all dive pictures should be imported into diving log from the same directory, e.g. E:\divelog\images\pictures.
	(To make it easy to know which picture is which, I also use filenames like 27_1.jpg, 27_2.jpg, 23_1.jpg, being the first and second pictures for dive number 27, and the first picture for dive number 23. But all that is required is that each picture filename be unique.)

	Set the path to your pictures on your web site in the file locations section of `config.inc.php`,
	for example...

	```
	$_config['picpath_web'] = "images/pictures/";
	```

	Upload your new pictures to the directory on your web server.
	In version of Diving Log prior to version 4.0.5, after you do your MySQL Dump from Diving Log, you would need to edit the .sql file produced to strip out the path from the picture filenames. For example, your full picture pathnames might be,

	```
	E:\divelog\images\pictures\27_1.jpg
	```

	Search for `E:\divelog\images\pictures\` and replace it with "", leaving just "27_1.jpg".
	However, with the changes to the Diving Log MySQL Dump option in version 4.0.5, you should no longer need to do this editing.  Save the edited MySQL Dump file and upload it into your web database.  

* EQUIPMENT PHOTOS Version 1.4+ supports equipment photos.  Use the same guidelines as for the dive pictures. 

* MAP IMAGES Version 1.4+ supports map images.  Use the same guidelines as for the dive pictures. 

* CERTIFICATION SCAN IMAGES Version 1.7+ supports map images.  Use the same guidelines as for the dive pictures. 

* METRIC & IMPERIAL UNITS Version 1.7+ supports display units as either metric or imperial.
	Diving Log stores all depth, pressure, weight, temperature and volume values in metric units. Thus metric units are exported via MySQL Dump and imported into your online MySQL database.

	However, like Diving Log, phpDivingLog gives you the option to display values using either metric or imperial units.  
	If you wish to have values display with imperial unit values, you will need to edit the 'Unit Conversion' values in the `config.inc.php` file.
	
	Set the unit configuration values to 'true' if you want to convert values and have them display as imperial units, or to 'false' if values should be displayed as metric units. 


* DIVE SUMMARY
	divesummary.php is a PHP script that can be called from a standard HTML page to show some dive log summary details in that web page, plus links to phpDivingLog.


* LANGUAGE COMPARE SCRIPT
	A language file comparison PHP script has been sourced and added.  Those of you either maintaining or building new language file for phpDivingLog will find this script useful.

	The script is `includes/languages/compare.php`  It needs to be where the language files are. Call it from the browser: http://example.com/divelog/includes/language/compare.php?f=danish to check "danish.inc.php" against "english.inc.php".


* DIVE PROFILE GRAPH
	Version 1.7+ supports the display of a dive profile graph without a background image. You can choose to provide a suitable 500 x 400 background image file (gif or jpg) and specify the path to it in the configuration file 'config.inc.php'.
	By default, phpDivingLog will use the setting for the length units in the configuration file to determine if metres or feet will be used in the dive profile graph. It will show the primary units as the left side Y scale, and the secondary units as the right side Y scale.
	Via settings in the configuration file `config.inc.php`, you can choose to show only the left Y scale, both the left and right Y scales with the same primary units, or with the primary units as the left side Y scale, and the secondary units as the right side Y scale (the default setting).


## Packages used in the Program
* [RTFClass v155](http://www.phpclasses.org/browse/file/7632.html) Rich Text Format - Parsing Class - (c) 2000 Markus Fischer
* [Highslide JS v4.1.4](http://highslide.com/support) Used to overlay dive pictures on the current page.
* [SmartyHost v3.1.30](http://www.smarty.net/)
* [jQuery Datatables](https://datatables.net/)

## Contributing

Please read [CONTRIBUTING.md](https://github.com/Infern1/phpDivinglog/blob/master/CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

## Authors
* **Olaf van Zandwijk**  - *Initial work*
* **Lloyd Borrett**  - [Lloyd](http://www.borrett.id.au/)
* **Rob Lensen**     - [Infern1](https://github.com/Infern1)


## License

This project is licensed under the GPL3 License 



