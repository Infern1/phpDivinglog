SELECT		Path, Description
FROM		$_config[table_prefix]Pictures
WHERE		LogID = '$globals[logid]'
ORDER BY	ID ASC
