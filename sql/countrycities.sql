SELECT		$_config[table_prefix].Logbook.CityID AS CityID,
		$_config[table_prefix].Logbook.City AS City
FROM		$_config[table_prefix]Logbook
WHERE		$_config[table_prefix]Logbook.CountryID = '$globals[countryid]'
AND		$_config[table_prefix]Logbook.CityID IS NOT NULL
GROUP BY	$_config[table_prefix]Logbook.CityID
ORDER BY	$_config[table_prefix]Logbook.City ASC

