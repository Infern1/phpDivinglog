SELECT		PhotoPath as Path, ShopName as Description
FROM		$_config[table_prefix]Shop
WHERE		ID = '$globals[id]'
ORDER BY	ID ASC
