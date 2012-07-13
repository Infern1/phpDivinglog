SELECT		$_config[table_prefix]Logbook.PlaceID, $_config[table_prefix]Logbook.Place
FROM		$_config[table_prefix]Logbook
WHERE		$_config[table_prefix]Logbook.CountryID = '$globals[countryid]'
AND		$_config[table_prefix]Logbook.PlaceID IS NOT NULL
GROUP BY	$_config[table_prefix]Logbook.PlaceID
ORDER BY	$_config[table_prefix]Logbook.Place ASC
