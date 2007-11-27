<?php
/**
 * Filename:      includes/language/german.inc.php
 * Function:      German language file for phpDivingLog.
 * @author        Sven Knoch - www.divinglog.de
 * @package phpdivinglog
 * @version $Rev$
 * Add. Changes:  Stefan Sypitzki - www.sypitzki.biz
 * Last Modified: $Date$
/***************************************************************************/

$_lang['yes'] = "Ja";
$_lang['no'] = "Nein";

// Character used for the price currency
$_lang['currency_prefix'] = "";
$_lang['currency_suffix'] = "€";

// Link Bar related values

$_lang['first'] = "Erster";
$_lang['last'] = "Letzter";
$_lang['previous'] = "Vorheriger";
$_lang['next'] = "N&auml;chster";

$_lang['first_dive_linktitle'] = "Erster Tauchgang";
$_lang['last_dive_linktitle'] = "Letzter Tauchgang";
$_lang['previous_dive_linktitle'] = "Vorheriger Tauchgang";
$_lang['next_dive_linktitle'] = "N&auml;chster Tauchgang";

$_lang['first_group_linktitle'] = "Erste Seite";
$_lang['last_group_linktitle'] = "Letzte Seite";
$_lang['previous_group_linktitle'] = "Vorherige Seite";
$_lang['next_group_linktitle'] = "N&auml;chste Seite";

$_lang['first_site_linktitle'] = "Erster Tauchplatz";
$_lang['last_site_linktitle'] = "Letzter Tauchplatz";
$_lang['previous_site_linktitle'] = "Vorheriger Tauchplatz";
$_lang['next_site_linktitle'] = "N&auml;chster Tauchplatz";

$_lang['first_equip_linktitle'] = "Erster Artikel";
$_lang['last_equip_linktitle'] = "Letzter Artikel";
$_lang['previous_equip_linktitle'] = "Vorheriger Artikel";
$_lang['next_equip_linktitle'] = "N&auml;chster Artikel";

$_lang['dive_log'] = "Logbuch";
$_lang['dive_sites'] = "Tauchpl&auml;tze";
$_lang['dive_stats'] = "Statistiken";
$_lang['dive_equip'] = "Ausr&uuml;stung";

$_lang['dive_log_linktitle'] = "Logbuch anzeigen";
$_lang['dive_sites_linktitle'] = "Tauchpl&auml;tze anzeigen";
$_lang['dive_stats_linktitle'] = "Statistiken anzeigen";
$_lang['dive_equip_linktitle'] = "Ausr&uuml;stung anzeigen";


// Dive Entry / Logbook related values

$_lang['dive_details_pagetitle'] = "Logbuch Eintrag f&uuml;r Tauchgang Nr ";

$_lang['logbook_divedate'] = "Datum:";
$_lang['logbook_entrytime'] = "Einstiegszeit:";
$_lang['logbook_divetime'] = "Tauchzeit:";
$_lang['logbook_depth'] = "Max. Tiefe:";
$_lang['logbook_place'] = "Tauchplatz:";
$_lang['logbook_city'] = "Ort / Insel:";
$_lang['logbook_country'] = "Land:";
$_lang['logbook_buddy'] = "Buddy/Buddies:";
$_lang['logbook_weather'] = "Wetter:";
$_lang['logbook_visibility'] = "Sicht:";
$_lang['logbook_altitude'] = "H&ouml;he &uuml;ber NN:";
$_lang['logbook_airtemp'] = "Lufttemp.:";
$_lang['logbook_water'] = "Gew&auml;sserart:";
$_lang['logbook_surface'] = "Wellen:";
$_lang['logbook_uwcurrent'] = "Str&ouml;mung:";
$_lang['logbook_watertemp'] = "Wassertemp.:";
$_lang['logbook_tanktype'] = "Flasche:";
$_lang['logbook_tanksize'] = "Flaschengr&ouml;sse:";
$_lang['logbook_gas'] = "Gasgemisch:";
$_lang['logbook_avgdepth'] = "Durchschn. Tiefe:";
$_lang['logbook_press'] = "Startdruck:";
$_lang['logbook_prese'] = "Enddruck:";
$_lang['logbook_presdiff'] = "Luftverbrauch:";
$_lang['logbook_sac'] = "AMV:";
$_lang['logbook_entry'] = "Einstiegsart:";
$_lang['logbook_deco'] = "Deco TG:";
$_lang['logbook_rep'] = "Rep. TG:";
$_lang['logbook_surfint'] = "Oberfl&auml;chenpause:";
$_lang['logbook_decostops'] = "Decostop Details:";
$_lang['logbook_weight'] = "Blei:";
$_lang['logbook_divesuit'] = "Anzug:";
$_lang['logbook_computer'] = "Tauchcomputer:";
$_lang['logbook_usedequip'] = "Ausr&uuml;stung bei diesem TG:";

$_lang['visibility'] = array("Gut", "Mittel", "Schlecht");
$_lang['water'] = array("Salz", "S&uuml;ss", "Misch");
$_lang['tanktype'] = array("Aluminium", "Stahl", "Carbon");
$_lang['entry'] = array("Ufer", "Boot");

$_lang['logbook_place_linktitle'] = "Details";

// based on the PHP date() function
$_lang['logbook_divedate_format'] = "D, d-M-Y";
$_lang['logbook_entrytime_format'] = "H:i";


// Dive Profile related values

$_lang['divepic_linktitle_pt1'] = "Bild ";
$_lang['divepic_linktitle_pt2'] = " von ";
$_lang['divepic_linktitle_pt3'] = " f&uuml;r TG Nr ";

$_lang['divepic_pt1'] = "Hier klicken zum anzeigen ";
$_lang['divepic_pt2s'] = " Bild ";
$_lang['divepic_pt2p'] = " Bilder ";
$_lang['divepic_pt3'] = " von diesem TG";

$_lang['dive_profile_title'] = "Tauchprofil für TG Nr ";
$_lang['dive_profile_xaxis_title'] = "Zeit (Minuten)";
$_lang['dive_profile_ymetric_title'] = "Tiefe (Metern)";
$_lang['dive_profile_yimperial_title'] = "Tiefe (Fuss)";
$_lang['dive_profile_depth_legend'] = "Tauchprofil";
$_lang['dive_profile_avgdepth_title'] = "Durchschn. Tiefe";
$_lang['dive_profile_ascent_legend'] = "Aufstiegs/Descent";
$_lang['dive_profile_deco_legend'] = "Deco";
$_lang['dive_profile_rbt_legend'] = "RBT";
$_lang['dive_profile_work_legend'] = "Work";

$_lang['dive_sect_conditions'] = "Bedingungen:";
$_lang['dive_sect_breathing'] = "Atmung:";
$_lang['dive_sect_details'] = "Tauchgang Details:";
$_lang['dive_sect_equipment'] = "Ausr&uuml;stung:";
$_lang['dive_sect_comments'] = "Kommentare:";


// Dive Log List related values

$_lang['dlog_none'] = "Keine Tauchgänge vorhanden.";

$_lang['dlog_title_number'] = "TG Nr";
$_lang['dlog_title_divedate'] = "Datum";
$_lang['dlog_title_depth'] = "Tiefe";
$_lang['dlog_title_divetime'] = "Zeit";
$_lang['dlog_title_location'] = "Ort";

$_lang['dlog_number_title'] = "Details f&uuml;r TG Nr ";

// based on the PHP date() function
$_lang['dlog_divedate_format'] = "d-M-Y";


// Dive Site related values

$_lang['dive_site_pagetitle'] = "Tauchplatz - ";

$_lang['place_place'] = "Tauchplatz:";
$_lang['place_city'] = "Ort / Insel:";
$_lang['place_country'] = "Land:";
$_lang['place_maxdepth'] = "Max. Tiefe:";
$_lang['place_lat'] = "Breitengrad:";
$_lang['place_lon'] = "L&auml;ngengrad:";
$_lang['place_map'] = "Karte:";

$_lang['mappic_linktitle'] = "Karte f&uuml;r ";
$_lang['mappic'] = "Karte anzeigen";

$_lang['site_dive_single'] = " Tauchgang an diesem Platz:";
$_lang['site_dive_plural'] = " Tauchg&auml;nge an diesem Platz:";

$_lang['site_sect_comments'] = "Kommentare:";
$_lang['site_google_link'] = "Google Map anzeigen von ";


// Dive Site List related values

$_lang['dsite_none'] = "No dive sites are currently available.";

$_lang['dsite_title_place'] = "Tauchplatz";
$_lang['dsite_title_city'] = "Ort / Insel";
$_lang['dsite_title_country'] = "Land";
$_lang['dsite_title_maxdepth'] = "Max. Tiefe";


// Dive Statistics related values

$_lang['stats_sect_stats'] = "Statistiken:";
$_lang['stats_sect_certs'] = "Brevets:";

$_lang['stats_totaldives'] = "Anzahl Tauchg&auml;nge:";
$_lang['stats_divedatemax'] = "Letzter Tauchgang:";
$_lang['stats_divedatemin'] = "Erster Tauchgang:";
$_lang['stats_totaltime'] = "Gesamte Tauchzeit:";
$_lang['stats_totaltime_units'] = "hh:mm";
$_lang['stats_divetimemax'] = "L&auml;ngster Tauchgang:";
$_lang['stats_divetimemin'] = "K&uuml;rzester Tauchgang:";
$_lang['stats_divetimeavg'] = "Durchschnittliche Tauchzeit:";
$_lang['stats_depthmax'] = "Max. Tiefe:";
$_lang['stats_depthmin'] = "Min. Tiefe:";
$_lang['stats_depthavg'] = "Durchschnittliche Tiefe:";
$_lang['stats_watertempmin'] = "K&auml;ltester Tauchgang.:";
$_lang['stats_watertempmax'] = "W&auml;rmster Tauchgang:";
$_lang['stats_shoredives'] = "Vom Ufer:";
$_lang['stats_boatdives'] = "Vom Boot:";
$_lang['stats_nightdives'] = "Nachttauchg&auml;nge:";
$_lang['stats_driftdives'] = "Strömungstauchg&auml;nge:";
$_lang['stats_deepdives'] = "Tieftauchg&auml;nge:";
$_lang['stats_cavedives'] = "H&ouml;hlentauchg&auml;nge:";
$_lang['stats_wreckdives'] = "Wracktauchg&auml;nge:";
$_lang['stats_photodives'] = "Fototauchg&auml;nge:";
$_lang['stats_saltwaterdives'] = "Salzwassertauchg&auml;nge:";
$_lang['stats_freshwaterdives'] = "Süsswassertauchg&auml;nge:";
$_lang['stats_brackishdives'] = "Brackwassertauchg&auml;nge:";
$_lang['stats_decodives'] = "Dekotauchg&auml;nge:";
$_lang['stats_repdives'] = "Wiederholungstauchg&auml;nge:";
$_lang['stats_depth1m'] = "0 - 18 m:";
$_lang['stats_depth2m'] = "19 - 30 m:";
$_lang['stats_depth3m'] = "31 - 40 m:";
$_lang['stats_depth4m'] = "41 - 55 m:";
$_lang['stats_depth5m'] = "> 55 m:";
$_lang['stats_depth1i'] = "0 - 60 fsw:";
$_lang['stats_depth2i'] = "61 - 100 fsw:";
$_lang['stats_depth3i'] = "101 - 130 fsw:";
$_lang['stats_depth4i'] = "131 - 180 fsw:";
$_lang['stats_depth5i'] = "> 180 fsw:";


// Dive Certification related value

$_lang['cert_brevet'] = "Brevet:";
$_lang['cert_org'] = "Organisation:";
$_lang['cert_certdate'] = "Datum:";
$_lang['cert_number'] = "Cert. #:";
$_lang['cert_instructor'] = "Instructor:";
$_lang['cert_inst_number'] = "Instructor #:";
$_lang['cert_scan_front'] = "scan front";
$_lang['cert_scan_back'] = "scan back";


// Dive Equipment related values

$_lang['equip_details_pagetitle'] = "Tauchausr&uuml;stung - ";
$_lang['equip_sect_comments'] = "Kommentare:";

$_lang['equip_object'] = "Artikel:";
$_lang['equip_manufacturer'] = "Hersteller:";
$_lang['equip_serial'] = "Seriennummer:";
$_lang['equip_datep'] = "Kaufdatum:";
$_lang['equip_dater'] = "Revision:";
$_lang['equip_warranty'] = "Garantie:";
$_lang['equip_shop'] = "Gekauft bei:";
$_lang['equip_price'] = "Preis:";
$_lang['equip_photo'] = "Bild:";
$_lang['equip_comments'] = "Kommentare:";

$_lang['equip_photo_linktitle'] = "Bild von ";
$_lang['equip_photo_link'] = "Bild anzeigen";

// based on the PHP date() function
$_lang['equip_date_format'] = "D, d.m.Y";


// Dive Equipment List related values

$_lang['equip_none'] = "Keine Ausr&uuml;stung vorhanden.";

$_lang['equip_title_object'] = "Artikel";
$_lang['equip_title_manufacturer'] = "Hersteller";


// Units

$_lang['unit_length'] = "Meter";
$_lang['unit_length_imp'] = "Fuss";
$_lang['unit_length_short'] = "m";
$_lang['unit_length_short_imp'] = "ft";
$_lang['unit_pressure'] = "Bar";
$_lang['unit_pressure_imp'] = "psi";
$_lang['unit_rate'] = "Liter/min";
$_lang['unit_rate_imp'] = "cu&nbsp;ft/min";
$_lang['unit_temp'] = "&deg;C";
$_lang['unit_temp_imp'] = "&deg;F";
$_lang['unit_volume'] = "Liter";
$_lang['unit_volume_imp'] = "cu&nbsp;ft";
$_lang['unit_weight'] = "kg";
$_lang['unit_weight_imp'] = "lbs";
$_lang['unit_time'] = "Minuten";
$_lang['unit_time_short'] = "min";


// Application ID related values

$_lang['poweredby'] = "Powered by ";
$_lang['and'] = " und ";

?>
