SELECT		PhotoPath as Path, Object as Description
FROM		$_config[table_prefix]Equipment
WHERE		ID = '$globals[id]'
ORDER BY	ID ASC
