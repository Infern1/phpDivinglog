SELECT		PhotoPath as Path, TripName as Description
FROM		$_config[table_prefix]Trip
WHERE		ID = '$globals[id]'
ORDER BY	ID ASC
