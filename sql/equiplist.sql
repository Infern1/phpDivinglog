SELECT		ID,
		Object,
		Manufacturer,
		Inactive,
		PhotoPath
FROM		$_config[table_prefix]Equipment
ORDER BY 	Inactive DESC, Object ASC, DateP ASC, Manufacturer ASC
