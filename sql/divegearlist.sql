SELECT		ID,
		Object
FROM		$_config[table_prefix]Equipment
WHERE 		ID IN ($globals[gearlist])
ORDER BY 	Manufacturer ASC, Object ASC
