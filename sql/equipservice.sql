SELECT		ID,
		Object,
		Manufacturer,
		DateRN,
		O2ServiceDate,
		Inactive,
		PhotoPath
FROM		$_config[table_prefix]Equipment
WHERE 		((to_days( now()) > (to_days(DateRN) - $_config[equipment_service_warning]))
OR 		(to_days( now()) > (to_days(O2ServiceDate) - $_config[equipment_service_warning])))
AND		(Inactive = 'False')
ORDER BY 	Inactive DESC, Object ASC, DateRN ASC, O2ServiceDate ASC
