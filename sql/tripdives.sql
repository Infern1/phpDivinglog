SELECT		Number
FROM		$_config[table_prefix]Logbook
WHERE		TripID = '$globals[tripid]'
ORDER BY	Number ASC
