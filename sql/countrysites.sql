SELECT		Number
FROM		$_config[table_prefix]Place AS p, $_config[table_prefix]Logbook AS l
WHERE		l.CountryID = '$globals[countryid]'
ORDER BY	Number ASC
