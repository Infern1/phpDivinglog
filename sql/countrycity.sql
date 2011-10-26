SELECT		Number,
		Country,
		CountryID,
		City,
		CityID
FROM		$_config[table_prefix]Logbook
WHERE		PlaceID = '$globals[placeid]'
LIMIT		1
