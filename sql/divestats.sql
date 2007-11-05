SELECT		MIN(Divedate) AS DivedateMin,
		MAX(Divedate) AS DivedateMax,
		MIN(Divetime) AS DivetimeMin,
		MAX(Divetime) AS DivetimeMax,
		AVG(Divetime) AS DivetimeAvg,
		SUM(Divetime) AS BottomTime,
		MIN(Depth) AS DepthMin,
		MAX(Depth) AS DepthMax,
		AVG(Depth) AS DepthAvg,
		MIN(Watertemp) AS WatertempMin,
		MAX(Watertemp) AS WatertempMax
FROM		$_config[table_prefix]Logbook
