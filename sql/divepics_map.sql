SELECT		MapPath as Path,Place, ID as PlaceID, Place as Description
FROM		$_config[table_prefix]Place
WHERE		ID = '$globals[id]'
ORDER BY	ID ASC
