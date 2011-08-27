SELECT		c.ID, c.Country, COUNT(*) 
FROM 		$_config[table_prefix]Country AS c, $_config[table_prefix]Logbook AS l
WHERE 		c.ID = l.CountryID
GROUP BY 	l.CountryID
ORDER BY 	c.Country ASC