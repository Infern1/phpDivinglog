SELECT		ID,
		Object,
		Manufacturer,
		Inactive
FROM		$_config[table_prefix]Equipment
ORDER BY 	Inactive DESC, Object ASC, DateP ASC, Manufacturer ASC
