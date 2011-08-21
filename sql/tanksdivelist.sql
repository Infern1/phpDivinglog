SELECT		*	
FROM		$_config[table_prefix]Tank
WHERE 		LogID = $globals[dive_id]
ORDER BY 	SortOrd ASC
