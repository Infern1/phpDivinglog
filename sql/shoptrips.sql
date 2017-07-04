SELECT		ID, TripName
FROM		$_config[table_prefix]Trip
WHERE		ShopID = '$globals[shopid]'
ORDER BY	TripName ASC
