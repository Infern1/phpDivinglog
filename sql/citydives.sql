SELECT		Number
FROM		$_config[table_prefix]Logbook
WHERE		CityID = '$globals[cityid]'
ORDER BY	Number ASC
