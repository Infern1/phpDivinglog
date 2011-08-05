SELECT		ID,
		Object,
		Manufacturer
FROM		$_config[table_prefix]Equipment
ORDER BY 	Object ASC, DateP ASC, Manufacturer ASC
