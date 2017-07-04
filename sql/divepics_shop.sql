SELECT		PhotoPath as Path, CONCAT(ShopType,': ',ShopName) as Description
FROM		$_config[table_prefix]Shop
WHERE		ID = '$globals[id]'
ORDER BY	ID ASC
