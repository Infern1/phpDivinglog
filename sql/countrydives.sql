SELECT		Number
FROM		$_config[table_prefix]Logbook
WHERE		CountryID = '$globals[countryid]'
ORDER BY	Number ASC
