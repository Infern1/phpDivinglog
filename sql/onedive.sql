SELECT		*
FROM		$_config[table_prefix]Logbook
WHERE		Number = '$globals[divenr]'
LIMIT		1
