SELECT		$_config[table_prefix].Logbook.CityID
FROM		$_config[table_prefix]Logbook
WHERE		$_config[table_prefix]Logbook.CityID = '$globals[cityid]'
AND		$_config[table_prefix]Logbook.CityID IS NOT NULL
GROUP BY	$_config[table_prefix]Logbook.CityID
ORDER BY	$_config[table_prefix]Logbook.CityID ASC
