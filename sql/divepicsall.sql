SELECT	    	a.LogID, a.Path, a.Description, b.ID, b.Number, b.PlaceID	
FROM		$_config[table_prefix]Pictures a, $_config[table_prefix]Logbook b
WHERE       	a.LogID = b.ID
ORDER BY	a.LogID ASC
