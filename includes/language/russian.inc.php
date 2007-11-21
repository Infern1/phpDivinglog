<?php
/**
 * Filename:  includes/language/english.inc.php
 * Function:  English language file for phpDivingLog.
 * @Author:    Lloyd Borrett - www.borrett.id.au
 * @package phpdivinglog
 * @version 2.0
 * Last Modified: 2007-03-07
*/

$_lang['yes'] = "Да";
$_lang['no'] = "Нет";

// Character used for the price currency
$_lang['currency_prefix'] = "";
$_lang['currency_suffix'] = " руб.";

// Link Bar related values

$_lang['first'] = "Начало";
$_lang['last'] = "Край";
$_lang['previous'] = "Пред.";
$_lang['next'] = "След.";

$_lang['first_dive_linktitle'] = "Первое погружение";
$_lang['last_dive_linktitle'] = "Крайнее погружение";
$_lang['previous_dive_linktitle'] = "Предыдущее погружение";
$_lang['next_dive_linktitle'] = "Следующее погружение";

$_lang['first_group_linktitle'] = "Первая группа";
$_lang['last_group_linktitle'] = "Крайняя группа";
$_lang['previous_group_linktitle'] = "Пред. группа";
$_lang['next_group_linktitle'] = "След. группа";

$_lang['first_site_linktitle'] = "Первый дайвсайт";
$_lang['last_site_linktitle'] = "Крайний дайвсайт";
$_lang['previous_site_linktitle'] = "Предыдущий дайвсайт";
$_lang['next_site_linktitle'] = "Следующий дайвсайт";

$_lang['first_equip_linktitle'] = "Первое наименование";
$_lang['last_equip_linktitle'] = "Крайнее наименование";
$_lang['previous_equip_linktitle'] = "Предыдущее наименование";
$_lang['next_equip_linktitle'] = "Следующее наименование";

$_lang['dive_log'] = "Логбук";
$_lang['dive_sites'] = "Дайвсайты";
$_lang['dive_stats'] = "Статистика";
$_lang['dive_equip'] = "Оборудование";
$_lang['diver_choice_linktitle'] = "Показать список логбуков";
$_lang['diver_choice'] = "Выбор логбука";

$_lang['dive_log_linktitle'] = "Показать логбук";
$_lang['dive_sites_linktitle'] = "Список дайвсайтов";
$_lang['dive_stats_linktitle'] = "Показать статистику";
$_lang['dive_equip_linktitle'] = "Список оборудования";


// Dive Entry / Logbook related values

$_lang['dive_details_pagetitle'] = "Запись в логбуке для погружения № ";

$_lang['logbook_divedate'] = "Дата:";
$_lang['logbook_entrytime'] = "Начало погружения:";
$_lang['logbook_divetime'] = "Длительность погружения:";
$_lang['logbook_depth'] = "Макс. глубина:";
$_lang['logbook_place'] = "Дайвсайт:";
$_lang['logbook_city'] = "Город/Остров:";
$_lang['logbook_country'] = "Страна:";
$_lang['logbook_buddy'] = "Бадди:";
$_lang['logbook_weather'] = "Погода:";
$_lang['logbook_visibility'] = "Видимость:";
$_lang['logbook_altitude'] = "Высота:";
$_lang['logbook_airtemp'] = "Темп. воздуха.:";
$_lang['logbook_water'] = "Вода:";
$_lang['logbook_surface'] = "Волны:";
$_lang['logbook_uwcurrent'] = "Течение:";
$_lang['logbook_watertemp'] = "Темп. воды:";
$_lang['logbook_tanktype'] = "Баллон:";
$_lang['logbook_tanksize'] = "Размер:";
$_lang['logbook_gas'] = "Параметры газа:";
$_lang['logbook_avgdepth'] = "Средняя глубина:";
$_lang['logbook_press'] = "Давл. входа:";
$_lang['logbook_prese'] = "Давл. выхода:";
$_lang['logbook_presdiff'] = "Потреблено газа:";
$_lang['logbook_sac'] = "Расход:";
$_lang['logbook_entry'] = "Вход:";
$_lang['logbook_deco'] = "Декостоп:";
$_lang['logbook_rep'] = "Повт. дайв:";
$_lang['logbook_surfint'] = "На поверхности перед погр.:";
$_lang['logbook_decostops'] = "Подробности декостопа:";
$_lang['logbook_weight'] = "Груз:";
$_lang['logbook_divesuit'] = "Костюм:";
$_lang['logbook_computer'] = "Компьютер:";
$_lang['logbook_usedequip'] = "Оборудование, исп. в дайве:";

$_lang['visibility'] = array("Хорошая", "Средняя", "Плохая");
$_lang['water'] = array("Соленая", "Пресная", "Смешанная");
$_lang['tanktype'] = array("Алюминий", "Сталь", "Карбон");
$_lang['entry'] = array("С берега", "С лодки");

$_lang['logbook_place_linktitle'] = "подробно";

// based on the PHP date() function
$_lang['logbook_divedate_format'] = "D, d-M-Y";
$_lang['logbook_entrytime_format'] = "H:i";


// Dive Profile related values

$_lang['divepic_linktitle_pt1'] = "Фото ";
$_lang['divepic_linktitle_pt2'] = " из ";
$_lang['divepic_linktitle_pt3'] = " дайва № ";

$_lang['divepic_pt1'] = "Щелкните, чтобы посмотреть ";
$_lang['divepic_pt2s'] = " фото ";
$_lang['divepic_pt2p'] = " фото ";
$_lang['divepic_pt3'] = " этого дайва";

$_lang['dive_profile_title'] = "Профиль погружения № ";
$_lang['dive_profile_xaxis_title'] = "Длительность (мин.)";
$_lang['dive_profile_ymetric_title'] = "Глубина (м)";
$_lang['dive_profile_yimperial_title'] = "Глубина (фт)";
$_lang['dive_profile_depth_legend'] = "Профиль";
$_lang['dive_profile_avgdepth_title'] = "Сред. глуб.";
$_lang['dive_profile_ascent_legend'] = "Быстр. подъем/Погр.";
$_lang['dive_profile_deco_legend'] = "Дека";
$_lang['dive_profile_rbt_legend'] = "RBT";
$_lang['dive_profile_work_legend'] = "Раб.";

$_lang['dive_sect_conditions'] = "Условия дайва:";
$_lang['dive_sect_breathing'] = "Дыхание:";
$_lang['dive_sect_details'] = "Подробности дайва:";
$_lang['dive_sect_equipment'] = "Оборудование:";
$_lang['dive_sect_comments'] = "Комментарии:";


// Dive Log List related values

$_lang['dlog_none'] = "Нет информации о дайвах.";

$_lang['dlog_title_number'] = "№";
$_lang['dlog_title_divedate'] = "Дата";
$_lang['dlog_title_depth'] = "Макс. глубина";
$_lang['dlog_title_divetime'] = "Время дайва";
$_lang['dlog_title_location'] = "Город/Остров";
$_lang['dlog_title_place'] = "Дайвсайт";

$_lang['dlog_number_title'] = "Подробности дайва № ";

// based on the PHP date() function
$_lang['dlog_divedate_format'] = "dMY";


// Dive Site related values

$_lang['dive_site_pagetitle'] = "Дайвсайт - ";

$_lang['place_place'] = "Дайвсайт:";
$_lang['place_city'] = "Город/Остров:";
$_lang['place_country'] = "Страна:";
$_lang['place_maxdepth'] = "Макс. глубина:";
$_lang['place_lat'] = "Широта:";
$_lang['place_lon'] = "Долгота:";
$_lang['place_map'] = "Карта:";

$_lang['mappic_linktitle'] = "Карта ";
$_lang['mappic'] = "Посмотреть карту";

$_lang['site_dive_single'] = " погружение на этом дайвсайте:";
$_lang['site_dive_plural'] = " погружений(я) на этом дайвсайте:";

$_lang['site_sect_comments'] = "Комментарии:";
$_lang['site_google_link'] = "Посмотреть на карте Google ";

$_lang['display_rows_dives'] = "Показаны дайвы с ";
$_lang['display_rows_divesites'] = "Показаны дайвсайты с ";
$_lang['display_rows_equipment'] = "Показано оборудование с ";

// Dive Site List related values

$_lang['dsite_none'] = "Нет информации о дайвсайтах.";

$_lang['dsite_title_place'] = "Дайвсайт";
$_lang['dsite_title_city'] = "Город/Остров";
$_lang['dsite_title_country'] = "Страна";
$_lang['dsite_title_maxdepth'] = "Макс. глубина";


// Dive Statistics related values

$_lang['stats_sect_stats'] = "Статистика погружений:";
$_lang['stats_sect_certs'] = "Сертификация дайвера:";

$_lang['stats_totaldives'] = "Общее количество погружений:";
$_lang['stats_divedatemax'] = "Крайнее погружение:";
$_lang['stats_divedatemin'] = "Первое погружение:";
$_lang['stats_totaltime'] = "Общее время погружений:";
$_lang['stats_totaltime_units'] = "чч:мм";
$_lang['stats_divetimemax'] = "Самый долгий дайв:";
$_lang['stats_divetimemin'] = "Самый короткий дайв:";
$_lang['stats_divetimeavg'] = "Среднее время дайва:";
$_lang['stats_depthmax'] = "Глубочайший дайв:";
$_lang['stats_depthmin'] = "Самый неглубокий дайв:";
$_lang['stats_depthavg'] = "Средняя глубина:";
$_lang['stats_watertempmin'] = "Самая холодная тем-ра воды:";
$_lang['stats_watertempmax'] = "Самая теплая тем-ра воды.:";
$_lang['stats_shoredives'] = "Дайвов с берега:";
$_lang['stats_boatdives'] = "Дайвов с лодки:";
$_lang['stats_nightdives'] = "Ночных дайвов:";
$_lang['stats_driftdives'] = "Дайвов с течением:";
$_lang['stats_deepdives'] = "Глубоководных дайвов:";
$_lang['stats_cavedives'] = "Дайвов в пещерах:";
$_lang['stats_wreckdives'] = "Дайвов на рэки:";
$_lang['stats_photodives'] = "Дайвов с фото:";
$_lang['stats_saltwaterdives'] = "Дайвов в соленой воде:";
$_lang['stats_freshwaterdives'] = "Дайвов в пресной воде:";
$_lang['stats_brackishdives'] = "Дайвов в смешанной воде:";
$_lang['stats_decodives'] = "Дайвов с декой:";
$_lang['stats_repdives'] = "Повторных дайвов:";
$_lang['stats_depth1m'] = "0 - 18 м:";
$_lang['stats_depth2m'] = "19 - 30 м:";
$_lang['stats_depth3m'] = "31 - 40 м:";
$_lang['stats_depth4m'] = "41 - 55 м:";
$_lang['stats_depth5m'] = "> 55 м:";
$_lang['stats_depth1i'] = "0 - 60 фт:";
$_lang['stats_depth2i'] = "61 - 100 фт:";
$_lang['stats_depth3i'] = "101 - 130 фт:";
$_lang['stats_depth4i'] = "131 - 180 фт:";
$_lang['stats_depth5i'] = "> 180 фт:";


// Dive Certification related value

$_lang['cert_brevet'] = "Сертификат:";
$_lang['cert_org'] = "Организация:";
$_lang['cert_certdate'] = "Дата:";
$_lang['cert_number'] = "№ сертификата:";
$_lang['cert_instructor'] = "Инструктор:";
$_lang['cert_inst_number'] = "№ сертификата инструктора:";
$_lang['cert_scan_front'] = "лиц. сторона";
$_lang['cert_scan_back'] = "обратная сторона";


// Dive Equipment related values

$_lang['equip_details_pagetitle'] = "Оборудование - ";
$_lang['equip_sect_comments'] = "Комментарий:";

$_lang['equip_object'] = "Наименование:";
$_lang['equip_manufacturer'] = "Производитель:";
$_lang['equip_serial'] = "Серийный номер:";
$_lang['equip_datep'] = "Дата покупки:";
$_lang['equip_dater'] = "Дата обслуживания:";
$_lang['equip_warranty'] = "Гарантия:";
$_lang['equip_shop'] = "Где приобретено:";
$_lang['equip_price'] = "Цена:";
$_lang['equip_photo'] = "Фото:";
$_lang['equip_comments'] = "Комментарий:";

$_lang['equip_photo_linktitle'] = "Фото ";
$_lang['equip_photo_link'] = "Посмотреть фото";

// based on the PHP date() function
$_lang['equip_date_format'] = "D, d-M-Y";


// Dive Equipment List related values

$_lang['equip_none'] = "Нет доступного оборудования.";

$_lang['equip_title_object'] = "Наименование";
$_lang['equip_title_manufacturer'] = "Производитель";


// Units

$_lang['unit_length'] = "метра (ов)";
$_lang['unit_length_imp'] = "фута (ов)";
$_lang['unit_length_short'] = "м";
$_lang['unit_length_short_imp'] = "фт";
$_lang['unit_pressure'] = "бар";
$_lang['unit_pressure_imp'] = "psi";
$_lang['unit_rate'] = "л/мин";
$_lang['unit_rate_imp'] = "куб. фт/мин";
$_lang['unit_temp'] = "�C";
$_lang['unit_temp_imp'] = "�F";
$_lang['unit_volume'] = "литра (ов)";
$_lang['unit_volume_imp'] = "куб. фт";
$_lang['unit_weight'] = "кг";
$_lang['unit_weight_imp'] = "фунтов";
$_lang['unit_time'] = "минут (ы)";
$_lang['unit_time_short'] = "мин";


// Application ID related values

$_lang['poweredby'] = "Powered by ";
$_lang['and'] = " and ";

// Grid

$_lang['grid_cancel']      = "Отмена";
$_lang['grid_close']       = "Закрыть";
$_lang['grid_save']        = "Сохранить";
$_lang['grid_saving']      = "Сохранение...";
$_lang['grid_loading']     = "Загрузка...";
$_lang['grid_edit']        = "Изменить";
$_lang['grid_delete']      = "Удалить";
$_lang['grid_add']         = "Добавить";
$_lang['grid_view']        = "Просмотр";
$_lang['grid_addRecord']   = "Добавить запись";
$_lang['grid_edtRecord']   = "Изменить запись";
$_lang['grid_chkRecord']   = "Просмотр записи";
$_lang['grid_false']       = "Нет";
$_lang['grid_true']        = "Да";
$_lang['grid_prev']        = "Пред.";
$_lang['grid_next']        = "След.";
$_lang['grid_confirm']     = "Удалить запись?";
$_lang['grid_search']      = "Поиск";
$_lang['grid_resetSearch'] = "Повторить поиск";
$_lang['grid_doublefield'] = "Обнаружены совпадающие поля";
$_lang['grid_norecords']   = "Записей не найдено";
$_lang['grid_errcode']     = "Ошибка данных [Некорректный код верификации]";
$_lang['grid_noinsearch']  = "Поиск по этому полю невозможен";
$_lang['grid_noformdef']   = "Для использования возможности 'отметить' Вы должны определить имя формы в функции Form";
$_lang['grid_cannotadd']   = "Невозможно добавить запись в эту таблицу";
$_lang['grid_cannotedit']  = "Невозможно редактировать запись в этой таблице";
$_lang['grid_cannotsearch']= "В этой таблице поиск не работает";
$_lang['grid_cannotdel']   = "Невозможно удаление записей в этой таблице";
$_lang['grid_sqlerror']    = "В запросе SQL найдена ошибка:";
$_lang['grid_errormsg']    = "Ошибка:";
$_lang['grid_errorscript'] = "Ошибка SQL в скрипте:";
$_lang['grid_display']     = "Показаны поля ";
$_lang['grid_to']          = "по";
$_lang['grid_of']          = "из";

?>
