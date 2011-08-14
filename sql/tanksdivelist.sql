SELECT		*	
FROM		$_config[table_prefix]Tank
WHERE 		LogID IN ($globals[dive_nr])
ORDER BY 	LogID ASC
