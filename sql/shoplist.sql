SELECT		ID,
		ShopName,
		ShopType,
                Country,
		PhotoPath
FROM		$_config[table_prefix]Shop
ORDER BY 	ShopName ASC
