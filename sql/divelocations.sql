SELECT		Number
FROM		$_config[table_prefix]Logbook
WHERE		PlaceID = '$globals[placeid]'
ORDER BY	Number ASC
