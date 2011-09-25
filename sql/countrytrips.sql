SELECT		ID, TripName
FROM		$_config[table_prefix]Trip
WHERE		CountryID = '$globals[countryid]'
ORDER BY	ID ASC
