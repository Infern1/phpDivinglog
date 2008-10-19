<?php /*
Copyright (c) 2007, Gurú Sistemas and/or Gustavo Adolfo Arcila Trujillo
All rights reserved.
www.gurusitemas.com

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer
	  in the documentation and/or other materials provided with the distribution.
    * Neither the name of the Gurú Sistemas Intl nor Gustavo Adolfo Arcila Trujillo nor the names of its contributors may be used to
	  endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS  "AS IS"  AND ANY EXPRESS  OR  IMPLIED WARRANTIES, INCLUDING, 
BUT NOT LIMITED TO,  THE IMPLIED WARRANTIES  OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.  IN NO EVENT
SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,  INDIRECT,  INCIDENTAL, SPECIAL, EXEMPLARY,  OR CONSEQUENTIAL 
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF  USE, DATA, OR PROFITS;  OR BUSINESS 
INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE 
OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE. 

phpMyDataGrid is Open Source released under the BSD License, and we need your help if you like this script and think to use it 
please make a donation. Our goal is To buy a house for 2 little childrens, so we need to collect USD 20.000 by receiving 4.000 
donations of USD 5 each, (If you think you can do a higher donation, don't think twice, just do it ;-) if you compare, you can 
find commercial versions with less features than phpMyDataGrid with prices higher than USD499.  So, just to make a donation is 
a cheap. 

Please remember donating is one way to show your support, copy and paste in your internet browser the following link to make your donation
https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=tavoarcila%40gmail%2ecom&item_name=phpMyDataGrid%202007&no_shipping=0&no_note=1&tax=0&currency_code=USD&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8

For more info, samples, tips, screenshots, help, contact, forum, please visit phpMyDataGrid site  
http://www.gurusistemas.com/indexdatagrid.php

For contact author: tavoarcila at gmail dot com or info at gurusistemas dot com
*/

class datagrid{
	var $dgVersion           = "2007a";     # phpMyDataGrid Version
	var $connectionHandler;	  				# Connection Identifier
	var $isADO	             = false;		# Tells to class if the connection was made by using ADODB class
	var $strMailErrors       = '';			# Mail to report SQL errors
	var $bolShowErrors       = true;		# Show in screen SQL errors
	var $tablename			 = '';			# Tablename
	var $where               = '';          # Where instruction(s) in query
	var $groupby             = '';          # GROUP BY instruction(s) in query
	var $orderColName        = '';          # Order field
	var $orderExpr           = '';          # Orientation in order
	var $fieldsArray         = array();		# Array with fields Info
	var $friendlyHTML        = false;       # If this is set to true, the HTML generated will have line breaks
	var $recno               = 0;			# Initial Record Number to show in grid.
	var $maxRec              = 20; 			# Number of records to show per page
	var $addBtn 			 = false;		# Tell to script if display the "NEW" button
	var $updBtn 			 = false;		# Tell to script if display the "EDIT" button
	var $delBtn 			 = false;		# Tell to script if display the "DELETE" button
	var $chkBtn 			 = false;		# Tell to script if display the "VIEW" button
	var $imgpath             = "images/";	# Defines the path where the images are located
	var $titulo	             = '';          # Title to show in header
	var $footer              = '';          # Title to show in footer
	var $search              = '';          # List of fields which can be used to perform searches
	var $pagination          = 'mixed';     # Pagination style 'links' 'select' 'mixed'
	var $orderArrows         = true;		# If true show ordering arrows
	var $parameters          = '';          # Parameters needed by script
	var $keyfield            = '';          # This must be set to an unique field to identify the database work
	var $closeTags           = false;		# If this is set to true the XHTML Tags like <IMG> <BR> will be closed with <IMG /> <BR /> etc.
	var $salt                = 'salt&pepper'; # String to add to MD5 Validations.
	var $FormName            = '';          # Defines if the generated HTML will be between <form> and </form> Tags
	var $doForm              = false;       # Defines if the generated HTML will be between <form> and </form> Tags
	var $methodForm          = 'POST';      # Defines method to use to call ajax. valid 'POST', 'GET'
	var $ajaxEditable        = '';			# If enabled, then user will be able to edit fields in place.
	var $linksperpage        = 5;           # Number of links to show contiguous in pagination
	var $decimalDigits       = 2;           # Number of decimal places in numbers
	var $decimalsep          = ".";         # Decimal separator
	var $dgAjaxChanged       = "#FF0000";   # Color to display when data is AJAX updated
	var $addonClic           = "DG_addrow()";  # Function to invoke when add button is pressed
	var $edtonClic			 = "DG_editrow(\"%s\",\"%s\")"; # Function to invoke when editrow button is pressed
	var $delonClic 			 = "DG_deleterow(\"%s\",\"%s\")"; # Function to invoke when delete button is pressed
	var $srconClic 			 = "DG_showSearchBox()"; # Function to invoke when search button is pressed
	var $vieonClic 			 = "DG_viewrow(\"%s\",\"%s\")"; # Function to invoke when view button is pressed
	var $actionCloseDiv      = "DG_sii(\"addDiv\",\"\");"; #Function to invoke when close button is closed when "adding", "editing" or "viewing"
	var $hasChart            = false;       # Defines if chart fields where defined
	var $hasCalcs            = "false";     # Defines if calculated fields where defined
	var $numerics			 = "0-1-2-3-4-double-float-integer-signed-count-percentage-promille";
	var $checkable           = false;       # Allows to select multiples rows simultaneous for make transactions
	var $poweredby           = false;        # Show in the last row the powered by phpMyDataGrid Leyend.
	var $showToOf            = true;        # Show message "Displaying record 1 to 20 of 241"
	var $sql                 = "";          # SQL To execute (optional, if this is empty then is build based on added fields.)
	var $totalize            = array();     # Define columns to totalize.
											# determines the numeric allowed mask
	var $color4Charts        = Array("#B69133","#74D06D","#99DDD2","#6491DA","#3ECC41","#467665","#268B2D","#DE8667","#5ADEA1","#E8A51A","#9A77E4",
									 "#7174E9","#A48081","#8ED912","#9D6BEA","#CD435D","#72B1DC","#DC583B","#6C931D","#BC8EE9","#A03B93","#3BEA37",
									 "#ABBDCD","#19CE39","#B9D29D","#D48C27","#B9D29D","#59AAE9","#27D909","#9C9CB2","#DED5AB","#DEB0D8","#9D7AD0",
									 "#AE51B0","#EDD77A","#456AB4","#D0CB8A","#27C372","#27C372","#DA2512","#5BAA48","#A97399","#03E511","#9EB612",
									 "#B03B97","#94C0C2","#008766","#56A956","#B4D111","#24DA58","#589C3E","#198177","#D216D2","#6DD964","#E2C0E7",
									 "#13B8B8","#A2B23C","#B1CC5A","#367A8C","#58E3AA","#738BDB","#57E128","#DA9C43","#A19A04","#9E97B2","#AD8051",
									 "#95C97A","#2EC3B0","#A93DD7","#BCC4B4");  // Predefined colors to show BarCharts.
	
	var $images = array(
			'add'         => 'add.png',
			'ajax'        => 'ajax.gif',
			'ASC'         => 'asc.png',
			'cancel'      => 'cancel.png',
			'close'       => 'close.png',
			'DESC'        => 'desc.png',
			'down'        => 'down.png',
			'down_off'    => 'down_off.png',
			'edit'        => 'edit.png',
			'erase'       => 'erase.png',
			'minidown'    => 'minidown.png',
			'miniup'      => 'miniup.png',
			'save'        => 'save.png',
			'search'      => 'search.png',
			'up'          => 'up.png',
			'up_off'      => 'up_off.png',
			'view'        => 'view.png',
			'save'        => 'save'
	);
	
	var $message = array( 
			'cancel' 	  => 'Cancel',
			'close' 	  => 'Close',
			'save'	 	  => 'Save',
			'saving'      => 'Saving . . .',
			'loading'     => 'Loading . . .',
			'edit'	 	  => 'Edit',
			'delete'  	  => 'Delete',
			'add'    	  => 'New',
			'view'    	  => 'View',
			'addRecord'	  => 'Add record',
			'edtRecord'	  => 'Edit record',
			'chkRecord'	  => 'View record',
			'false'       => 'No',
			'true'        => 'Yes',
			'prev'        => 'Previous',
			'next'        => 'Next',
			'confirm'	  => 'Delete record?',
			'search' 	  => 'Search', 
			'resetSearch' => 'Reset Search',
			'doublefield' => 'Duplicate field definition',
			'norecords'   => 'No records found',
			'errcode'     => 'Error in Data [Incorrect verification code]',
			'noinsearch'  => 'Field not available for search',
			'noformdef'   => 'To use "checkable" feature you must define a FORM name by using Form Function',
			'cannotadd'   => 'Can not add records to this grid',
			'cannotedit'  => 'Can not edit records in this grid',
			'cannotsearch'=> 'Can not make searchs in this grid',
			'cannotdel'   => 'Can not delete records in this grid',
			'sqlerror'    => 'SQL Error found in query:',
			'errormsg'    => 'Error Message:',
			'errorscript' => 'SQL Error in script:',
			'display'     => 'Displaying rows',
			'to'          => 'to',
			'of'          => 'of'
	);
	
	# language: Defines the language to show messages
	function language($language){
		switch ($language){
			case 'nederlands': case 'ne' : case 'dutch' :
				// This translation thanks to Ben Mulder from nederlands
				$this->message=array(
					'cancel'      => 'Annuleren',
					'close' 	  => 'Sluit',
					'save'        => 'Ppslaan',
					'saving'      => 'Bezig met opslaan . . .',
					'loading'     => 'Laden . . .',
					'edit'        => 'Wijzig',
					'delete'      => 'Verwijder',
					'add'         => 'Nieuw',
					'view'    	  => 'Mening',
					'addRecord'   => 'Record toevoegen',
					'edtRecord'   => 'Record wijzigen',
					'chkRecord'	  => 'Record meningen',
					'false'       => 'Nee',
					'true'        => 'Ja',
					'prev'        => 'Vorige',
					'next'        => 'Volgende',
					'confirm'     => 'Record verwijderen?',
					'search'      => 'Zoeken',
					'resetSearch' => 'Nieuwe zoekterm',
					'doublefield' => 'Dubbele veldnaam',
					'norecords'   => 'Geen records gevonden',
					'errcode'     => 'Fout in data [Incorrecte verificatiecode]',
					'noinsearch'  => 'Zoeken niet beschikbaar voor deze kolom',
					'noformdef'   => 'Benoem een FORMnaam voor gebruik van de checkbox',
					'cannotadd'   => 'Record toevoegen niet beschikbaar',
					'cannotedit'  => 'Record wijzigen niet beschikbaar',
					'cannotsearch'=> 'Zoeken niet beschikbaar',
					'cannotdel'   => 'Record verwijderen niet beschikbaar',
					'sqlerror'    => 'SQL fout in query:',
					'errormsg'    => 'Foutmelding:',
					'errorscript' => 'SQL fout in script:',
					'display'     => 'Het tonen van rijen',
					'to'          => 'tot',
					'of'          => 'van'
					);
				break;
			case 'deutch': case 'de' :
				// This translation thanks to Wilfried Steinhoff from Germany
				$this->message=array(
					'cancel'       => 'Abbrechen',
					'close' 	   => 'Ende',
					'save'         => 'Speichern',
					'saving'       => 'speichern läuft . . .',
					'loading'      => 'Laden läuft . . .',
					'edit'         => 'Bearbeiten',
					'delete'       => 'Löschen',
					'add'          => 'Neu',
					'view'    	   => 'Blick',
					'addRecord'    => 'Datensatz hinzufügen',
					'edtRecord'    => 'Datensatz bearbeiten',
					'chkRecord'	   => 'Datensatz blicken',
					'false'        => 'Nein',
					'true'         => 'Ja',
					'prev'         => 'Vorheriger',
					'next'         => 'Nächster',
					'confirm'      => 'Datensatz entfernen?',
					'search'       => 'Suchen',
					'resetSearch'  => 'Suchfelder löschen',
					'doublefield'  => 'Feldbeschreibung schon vorhanden',
					'norecords'    => 'Kein Datensatz gefunden',
					'errcode'      => 'Datenfehler [Prüfung]',
					'noinsearch'   => 'kein Suchfeld ....',
					'noformdef'    => '"checkable" feature erwartet eine "FORM" - "Form" Funktion benutzen',
					'cannotadd'    => 'Datensatz kann nicht hinzugefügt werden',
					'cannotedit'   => 'Datensatz kann nicht bearbeitet werden',
					'cannotsearch' => 'Suchen nicht zugelassen',
					'cannotdel'    => 'Löschen nicht möglich',
					'sqlerror'     => 'Fehler in SQL-Abfrage:',
					'errormsg'     => 'Fehlermeldung:',
					'errorscript'  => 'Fehler in SQL-Script:',
					'display'      => 'Anzeigen von von Reihe',
					'to'           => 'zu',
					'of'           => 'von'
				);
				break; 
			case 'español': case 'es' :
				// This translation thanks to Gustavo Arcila from Colombia - Oh! is me!!!
				$this->message = array( 
					'cancel' 	  => 'Cancelar',
					'close' 	  => 'Cerrar',
					'save'	 	  => 'Grabar',
					'saving'      => 'Grabando . . .',
					'loading'     => 'Cargando . . .',
					'edit'	 	  => 'Editar',
					'delete'  	  => 'Borrar',
					'add'    	  => 'Adicionar',
					'view'    	  => 'Ver',
					'addRecord'	  => 'Adicionar Registro',
					'edtRecord'	  => 'Editar Registro',
					'chkRecord'	  => 'Ver registro',
					'false'       => 'No',
					'true'        => 'Si',
					'prev'        => 'Anterior',
					'next'        => 'Siguiente',
					'confirm'	  => 'Eliminar Registro?',
					'search' 	  => 'Buscar', 
					'resetSearch' => 'Cancelar B&uacute;squeda',
					'doublefield' => 'Campo duplicado',
					'norecords'   => 'No se encontraron registros',
					'errcode'     => 'Error de datos [C&oacute;digo de verificaci&oacute;n no v&aacute;lido]',
					'noinsearch'  => 'Campo no disponible para b&uacute;squedas',
					'noformdef'   => 'Para usar la funci&oacute;n "checkable" debe definir un nombre de FORM usando la funcion "Form"',
					'cannotadd'   => 'No puede adicionar registros a esta grilla',
					'cannotedit'  => 'No puede editar registros a esta grilla',
					'cannotsearch'=> 'No puede realizar b&uacute;squedas en esta grilla',
					'cannotdel'   => 'No puede eliminar registros de esta grilla',
					'sqlerror'    => 'Error SQL encontrado en el query:',
					'errormsg'    => 'Mensaje de error:',
					'errorscript' => 'Error SQL en el archivo:',
					'display'     => 'Mostrando registros',
					'to'           => 'a',
					'of'           => 'de'
				);
				break;
			case 'francais': case 'fr' :
				// This translation thanks to Christophe LE RAT from France
				$this->message = array( 
					'cancel'       => 'Annuler',
					'close' 	   => 'Fin',
					'save'         => 'Enregistrer',
					'saving'       => 'Enregistrement en cours . . .',
					'loading'      => 'Chargement en cours . . .',
					'edit'         => 'Modifier',
					'delete'       => 'Supprimer',
					'add'          => 'Ajouter',
					'view'    	   => 'Regard',
					'addRecord'    => 'Ajouter un enregistrement',
					'edtRecord'    => 'Modifier un enregistrement',
					'chkRecord'	   => 'Regard un enregistrement',
					'false'        => 'Non',
					'true'         => 'Oui',
					'prev'         => 'Précédent',
					'next'         => 'suivant',
					'confirm'      => 'Confirmez-vous la suppression?',
					'search'       => 'Rechercher',
					'resetSearch'  => 'Réinitialiser la recherche',
					'doublefield'  => 'Le champ est en double',
					'norecords'    => 'aucun enregistrement trouvé',
					'errcode'      => 'Erreur dans les données [Code de vérification incorrect]',
					'noinsearch'   => 'Champ indisponible pour la fonction de recherche',
					'noformdef'    => 'Pour utiliser une fonction disponible via une case à cocher, vous devez définir un nom de formulaire en utilisant le fonction "Form"',
					'cannotadd'    => "Impossible d'ajouter un enregistrement dans cette grille",
					'cannotedit'   => "Impossible de modifier un enregistrement dans cette grille",
					'cannotsearch' => "Impossible d'effectuer une recherche dans cette grille",
					'cannotdel'    => "Impossible de supprimer un enregistrement dans cette grille",
					'sqlerror'     => "Une erreur SQL s'est déclarée dans cette requête :",
					'errormsg'     => "Message d'erreur:",
					'errorscript'  => 'Erreur SQL dans ce script:',
					'display'      => 'Montrer des rangées',
					'to'           => 'à',
					'of'           => 'de'
				);
				break;
			case 'italian' : case 'it' : 
				// This translation thanks to Luca Colangiuli from Bari, Italy
				$this->message = array( 
					'cancel'       => 'Cancella',
					'save'         => 'Salva',
					'saving'       => 'Salvataggio in corso . . .',
					'loading'      => 'Caricamento in corso . . .',
					'edit'         => 'Modifica',
					'delete'       => 'Elimina',
					'add'          => 'Nuovo',
					'view'    	   => 'Sguardo',
					'addRecord'    => 'Aggiungi record',
					'edtRecord'    => 'Modifica record',
					'chkRecord'	   => 'Sguardo record',
					'false'        => 'No',
					'true'         => 'Si',
					'prev'         => 'Precedente',
					'next'         => 'Successivo',
					'confirm'      => 'Eliminare il record?',
					'search'       => 'Cerca',
					'resetSearch'  => 'Ripristina ricerca',
					'doublefield'  => 'Definizione di campo duplicata',
					'norecords'    => 'Nessun record trovato',
					'errcode'      => 'Errore nei dati [Codice di verifica errato]',
					'noinsearch'   => 'Campo non disponibile per la ricerca',
					'noformdef'    => 'Per usare la funzione  "checkable" devi definire un nome di FORM usando le funzioni "Form"',
					'cannotadd'    => 'Non posso aggiungere record a questa griglia',
					'cannotedit'   => 'Non posso modificare record in questa griglia',
					'cannotsearch' => 'Non posso effettuare ricerche in questa griglia',
					'cannotdel'    => 'non posso cancellare record in questa griglia',
					'sqlerror'     => 'Errore SQL nella query:',
					'errormsg'     => 'Messaggio di errore:',
					'errorscript'  => 'Errore SQL nello script:' ,
					'display'      => 'Visualizzazione delle file',
					'to'           => 'a',
					'of'           => 'di'
				);
				break;
            case 'russian': case 'ru' :
                $this->message=array(
                        'cancel'      => 'Otmena',
                        'close'      => 'Zakryt',
                        'save'     => 'Sohranit',
                        'saving'      => 'Sohranenie . . .',
                        'loading'     => 'Zagruzka . . .',
                        'edit'     => 'Izmenit',
                        'delete'       => 'Udalit',
                        'add'         => 'Dobavit',
                        'view'         => 'Prosmotr',
                        'addRecord'     => 'Dobavit zapis',
                        'edtRecord'     => 'Izmenit zapis',
                        'chkRecord'     => 'Prosmotr zapisi',
                        'false'       => 'Net',
                        'true'        => 'Da',
                        'prev'        => 'Pred.',
                        'next'        => 'Sled.',
                        'confirm'     => 'Udalit zapis',
                        'search'      => 'Poisk',
                        'resetSearch' => 'Povtorit poisk',
                        'doublefield' => 'Obnaruzheny odinakovye polya',
                        'norecords'   => 'Zapisej ne najdeno',
                        'errcode'     => 'Oshibka dannyh [Nekorrektnyj kod verifikacii]',
                        'noinsearch'  => 'Poisk po `etomu polyu nevozmozhen',
                        'noformdef'   => 'Dlya ispol zovaniya vozmozhnosti "otmetit" Vy dolzhny opredelit imya formy ispol zuya Form Function',
                        'cannotadd'   => 'Nevozmozhno dobavit zapis v `etu tablicu',
                        'cannotedit'  => 'Nevozmozhno redaktirovat zapis v `etoj tablice',
                        'cannotsearch'=> 'V `etoj tablice poisk ne rabotaet',
                        'cannotdel'   => 'Nevozmozhno udalenie zapisej v `etoj tablice',
                        'sqlerror'    => 'V zaprose SQL najdena oshibka:',
                        'errormsg'    => 'Oshibka:',
                        'errorscript' => 'Oshibka SQL v skripte:',
                        'display'     => 'Pokazany polya',
                        'to'          => iconv("WINDOWS-1251","UTF-8",'ïî'),
                        'of'          => 'èç'
                            );
                break;

			case 'èeština': case 'cs' :
				// This translation thanks to Pampuch.cz from Czech Republic
				$this->message=array(
					'cancel' 	  => 'Storno',
					'close' 	  => 'Zavøít',
					'save'	 	  => 'Uložit',
					'saving'      => 'Ukládání . . .',
					'loading'     => 'Naèítání . . .',
					'edit'	 	  => 'Zmìnit',
					'delete'  	  => 'Smazat',
					'add'    	  => 'Nový',
					'view'    	  => 'Náhled',
					'addRecord'	  => 'Pøidat záznam',
					'edtRecord'	  => 'Zmìnit záznam',
					'chkRecord'	  => 'Náhled záznamu',
					'false'       => 'Ne',
					'true'        => 'Ano',
					'prev'        => 'Pøedchozí',
					'next'        => 'Další',
					'confirm'	  => 'Smazat záznam?',
					'search' 	  => 'Hledat', 
					'resetSearch' => 'Smazat hledání',
					'doublefield' => 'Zdvojený záznam v poli',
					'norecords'   => 'Nenalezen žádný záznam',
					'errcode'     => 'Chyba dat [Neplatný ovìøovací kód]',
					'noinsearch'  => 'Nelze hledat v tomto poli',
					'noformdef'   => 'K použití "zaškrtnutí" musíte definovat jméno formuláøe použitím "Form" funkce',
					'cannotadd'   => 'Nemùžu pøidat záznam v tété tabulce',
					'cannotedit'  => 'Nemùžu mìnit záznamy v tété tabulce',
					'cannotsearch'=> 'Nemùžu hledat v tété tabulce',
					'cannotdel'   => 'Nemùžu smazat záznam v tété tabulce',
					'sqlerror'    => 'SQL chyba nalezena v pøíkazu:',
					'errormsg'    => 'Chybová zpráva:',
					'errorscript' => 'SQL chyba ve skriptu:',
					'display'     => 'Zobrazení øádkù',
					'to'          => 'do',
					'of'          => 'z'
				);
				break; 
			case 'portuguese' : case 'pt' : case 'pt_br' : 
				// This translation thanks to Paulo Henrique Garcia from Brazil
			   $this->message=array(
                        'cancel'      => 'Cancelar',
                        'close'       => 'Fechar',
                        'save'        => 'Salvar',
                        'saving'      => 'Savando . . .',
                        'loading'     => 'Carregando . . .',
                        'edit'        => 'Editar',
                        'delete'      => 'Excluir',
                        'add'         => 'Novo',
                        'view'        => 'Ver',
                        'addRecord'   => 'Adicionar registro',
                        'edtRecord'   => 'Editar registro',
                        'chkRecord'   => 'Ver registro',
                        'false'       => 'Não',
                        'true'        => 'Sim',
                        'prev'        => 'Anterior',
                        'next'        => 'Próximo',
                        'confirm'     => 'Excluir Registro?',
                        'search'      => 'Pesquisar',
                        'resetSearch' => 'Apagar Pesquisa',
                        'doublefield' => 'Definição de campos duplicados',
                        'norecords'   => 'Nenhum registro encontrador',
                        'errcode'     => 'Erro nos Dados [Código de verificalção incorreto]',
                        'noinsearch'  => 'Campo não disponível para pesquisa',
                        'noformdef'   => 'Para usar a funcionalidade "checkable" você precisa definir um nome de FORM usando a "Form Function"',
                        'cannotadd'   => 'Não é possível adicionar registros a esta grid',
                        'cannotedit'  => 'Não é possível editar registros nesta grid',
                        'cannotsearch'=> 'Não é possível pesquisar registros nesta grid',
                        'cannotdel'   => 'Não é possível excluir registros nesta grid',
                        'sqlerror'    => 'Erro SQL encontrado em query:',
                        'errormsg'    => 'Mensagem de Erro:',
                        'errorscript' => 'Erro SQL no script:',
                        'display'     => 'Exibindo linhas',
                        'to'          => 'até',
                        'of'          => 'de'
                  );				
			 	break;
		}
	}
	
	# conectadb: This function is used to connect with the database, you can connect by using the native mysql in php or by using ADO DB class
	function conectadb($strServer, $strUsername, $strPassword, $strDatabase, $bolUseADOdb=false, $strType='mysql', $intPort=3306){
		if (!$bolUseADOdb){
			$this->connectionHandler = mysql_connect($strServer, $strUsername, $strPassword) or 
				$this->SQLerror("mysql_connect('$strServer:$intPort', $strUsername, $strPassword))", mysql_error());
			mysql_select_db($strDatabase, $this->connectionHandler) or 
				$this->SQLerror("mysql_select_db($strDatabase, $this->connectionHandler)", mysql_error());
                mysql_query("SET NAMES 'utf8'");
                mysql_query("SET CHARACTER SET 'utf8'");
                mysql_query("SET COLLATION_CONNECTION = 'utf8_general_ci'");
		}else{
			$this->isADO = true;
			$this->connectionHandler = &ADONewConnection($strType);  # create the connection
	        $this->connectionHandler -> Connect($strServer, $strUsername, $strPassword, $strDatabase);
		}
	}
	
	# desconectar: This function is used to disconnect the database
	function desconectar(){
		if ($this->isADO)
			$this->connectionHandler -> close();
		else
			mysql_close( $this->connectionHandler) or 
				$this->SQLerror("mysql_close( $this->connectionHandler)", mysql_error());
	}
	
	# SQLerror: This function is used internally to handle SQL errors
	function SQLerror($strQuery, $strError){
		$sl = ($this->closeTags)?"/":"";						    # This variable must be inserted on each XHTML field which need to be closed with an / (<img ... />).
		$errMsg = "<div id='DG_sqlerror' class='dgError'><strong>".$this->message["sqlerror"]."</strong> $strQuery<br $sl><br $sl><strong>".$this->message["errormsg"]." </strong>$strError</div>";
		if (!empty($this->strMailErrors)){
			$strSubjet=$this->message["errorscript"]." ".basename($_SERVER["PHP_SELF"]);
			mail($this->strMailErrors, $strSubjet, $errMsg);
		}
		if ($this->bolShowErrors) echo $errMsg;
		die();
	}

	# reportSQLErrorsTo: Define an e-mail to report any SQL errors
	function reportSQLErrorsTo($strMail, $bolShow=true){
		$this->strMailErrors = $strMail;
		$this->bolShowErrors = $bolShow;
	}
	
	# sqlstatement: Set the SELECT SQL instructions to execute#
	function sqlstatement($sql){
		$this->sql = $sql;
	}

	# tabla: Defines the table name to make the query
	function tabla($tabla){
		$this->tablename = $tabla;
	}

	# where: Defines the where statement in query
	function where($strWhere){
		$this->where= $strWhere;
	}
	
	# groupby: Defines the GROUP BY statement in query
	function groupby($strGroup){
		$this->groupby= $strGroup;
	}

	# orderby: Defines the ordering field and orientation
	function orderby($field, $order="ASC"){
		$this->orderColName = $field;
		if (!($order=="ASC" or $order=="DESC")) $order="ASC";
		$this->orderExpr    = $order;
	}

	# keyfield: This must be set to an unique field to identify the database rows
	function keyfield($keyfield){
		$this->keyfield = $keyfield;
	}

	# setAction: used to change the original function of buttons
	function setAction($action, $event){
		$action = strtolower($action);
		if (in_array($action, array("add", "edit", "delete", "search", "view"))) {
			switch ($action){
				case "add"   : $this->addonClic = $event; break;
				case "edit"  : $this->edtonClic = $event; break;
				case "delete": $this->delonClic = $event; break;
				case "search": $this->srconClic = $event; break;
				case "view"  : $this->vieonClic = $event; break;
			}
		}
	}
	
	# validField: Determines if a field is in Fieldlist or Not to avoid SQL injection
	function validField($strfieldName){
		if (in_array($strfieldName, $this->getFields())) return true; else return false;
	}

	# decimalDigits: Number of decimal places in numbers
	function decimalDigits($digits){
		$this->decimalDigits = $digits;
	}

	# decimalPoint: Defines the decimal separator
	function decimalPoint($char){
		$this->decimalsep=$char; 
	}

	# getFields: returns an array with fields defined in FormatColumn
	function getFields($filter=''){
		$arrFilter = explode(",",$filter);
		$arrField = array();
		foreach($this->fieldsArray as $value) {
			$fldType = $value["inputtype"]; 
			if (empty($filter)){
				$arrField[] = $value["strfieldName"]; 
			}else{
				if(in_array($fldType, $arrFilter)){
					$arrField[] = $value["strfieldName"]; 
				}
			}
		}
		return $arrField;
	}	
	
	# friendlyHTML: Defines if make linebreaks on generated HTML
	function friendlyHTML(){
		$this->friendlyHTML = true;
	}

	# closeTags: If this is set to true the XHTML Tags like <IMG> <BR> will be closed with <IMG /> <BR /> etc.
	function closeTags($bolStat){
		$this->closeTags = $bolStat;
	}
	
	# Form: Defines if the generated HTML will be between <form> and </form> Tags
	function Form($formName, $doForm=true){
		$this->doForm = $doForm;
		$this->FormName = $formName;
	}

	# methodForm: Defines METHOD to use to call ajax. valid 'POST', 'GET'
	function methodForm($methodForm){
		$this->methodForm = strtoupper($methodForm);
		if (!($methodForm=="POST" or $methodForm=="GET")) $methodForm="GET";
	}

	# salt: String to add to MD5 Validations.
	function salt($salt){
		$this->salt = $salt;
	}

	# total: Defines the fields to be totalized per page in the grid.
	function total($fields){
		$this->totalize = explode(",",$fields);
	}
	
	# AjaxChanged: Color to display when data is AJAX updated
	function AjaxChanged($strColor){
		$this->dgAjaxChanged = $strColor;
	}

	# checkable: Allows to select multiples rows simultaneous for make transactions
	function checkable($status=true){
		if (empty($this->FormName))
			die("<div class='dgError'>".$this->message['noformdef']."</div>");
		else
			$this->checkable = $status;
	}

	# TituloGrid: Defines the title to show in the Grid Header
	function TituloGrid($titulo){
		$this->titulo = $titulo;
	}

	# FooterGrid: Defines the footer to show in the Grid 
	function FooterGrid($text){
		$this->footer = $text;
	}

	# linksperpage: Defines the number of links to show contiguous in pagination
	function linksperpage($amount){
		$this->linksperpage = $amount;
	}

	# pathtoimages: Defines the path for images
	function pathtoimages($path){
		$this->imgpath = $path;
	}
	
	# noorderarrows: if this function is called, the ordering arrows will not be show
	function noorderarrows(){
		$this->orderArrows = false;
	}

	# datarows: Is used to define the amount of records to be shown in the grid
	function datarows($intLines){
		$this->maxRec = $intLines;
	}

	# buttons: Define what buttons do you want to show
	function buttons($bolAdd,$bolUpd,$bolDel,$bolChk=false){
		$this->addBtn = $bolAdd;
		$this->updBtn = $bolUpd;
		$this->delBtn = $bolDel;
		$this->chkBtn = $bolChk;
	}
    
    /**
     * Check if a file exists in the include path
     *
     * @version     1.2.1
     * @author      Aidan Lister <aidan@php.net>
     * @link        http://aidanlister.com/repos/v/function.file_exists_incpath.php
     * @param       string     $file       Name of the file to look for
     * @return      mixed      The full path if file exists, FALSE if it does not
     */ 
    function file_exists_incpath ($file)
    {
        $paths = explode(PATH_SEPARATOR, get_include_path());

        foreach ($paths as $path) {
            // Formulate the absolute path
            $fullpath = $path . DIRECTORY_SEPARATOR . $file;

            // Check it
            if (file_exists($fullpath)) {
                return $fullpath;
            }
        }

        return false;
    }
	# paginationmode: Defines the pagination style
	function paginationmode ($pgm){
		$pgm=strtolower($pgm);
		if (!in_array($pgm, array("links","select","mixed"))) $pgm="mixed";
		$this->pagination=$pgm;
	}

	# FormatColumn: Define fields to show and their settings
	function FormatColumn($strfieldName, $strHeader, $fieldWidth=0, $maxlength=0, $inputtype=0, $columnwidth=0, $align= 'center', $Mask='text', $default='', $cutChar=0){
		if ( $strfieldName=="" or !$this->validField( $strfieldName)){
			$mask = strtolower($Mask);
			$this->fieldsArray["$strfieldName"]["strfieldName"] = $strfieldName; 		  # Field Name
			$this->fieldsArray["$strfieldName"]["strHeader"]    = $strHeader; 			  # Title to show in top of grid
			$this->fieldsArray["$strfieldName"]["fieldWidth"]   = $fieldWidth;			  # Input size
			$this->fieldsArray["$strfieldName"]["maxlength"]    = $maxlength; 			  # Input maxlength
			$this->fieldsArray["$strfieldName"]["columnwidth"]  = intval($columnwidth)."px"; # Column width
			$this->fieldsArray["$strfieldName"]["align"]        = $align;                 # Left, center, right, justify
			$this->fieldsArray["$strfieldName"]["mask"]         = $Mask;                  # Mask for data output
			$this->fieldsArray["$strfieldName"]["default"]      = $default;  			  # Default value for new records
			$this->fieldsArray["$strfieldName"]["select"]       = '';                     # Auxiliar field for data in masks (check, select)

			$this->fieldsArray["$strfieldName"]["cutChar"]      = $cutChar;               # Amount of chars to show.
	
			$datatype='text';
			if ($mask=='textarea') $datatype='textarea';
			if (substr($mask,0,5)=='image') $datatype='image';
			if (substr($mask,0,9)=='imagelink'){ $datatype='imagelink'; }
			$pmask = !(strpos($this->numerics,trim($mask)) === false);
			if (substr($mask,0,5)=='money' or $pmask)$datatype='number';
			if (substr($mask,0,4)=='sign'  or $pmask)$datatype='number';
	    	if (substr($mask,0,4)=='date') $datatype='date';
			if (substr($mask,0,4)=='link') $datatype='link';
			if (substr($mask,0,4)=='calc') {$datatype='calc'; $this->hasCalcs = "true"; echo "<script type='text/javascript'> var thereisCalc = true;</script>";  $inputtype=3; }
			if (substr($mask,0,5)=='chart'){$datatype='chart'; $this->hasChart = true; $inputtype=5; 
				if (strpos($mask,':') > 0) {
					$arrMask=explode(':',$Mask); $arrMask=array_slice($arrMask,1);
				}else{ 
					$arrMask=array("none:sum");
				}
				$this->fieldsArray["$strfieldName"]["select"] = $arrMask;
			}		
			if (substr($mask,0,4)=='bool' or substr($mask,0,5)=='check'){ $datatype='check';
				if (strpos($mask,':') > 0) {
					$arrMask=explode(':',$Mask); $arrMask=array_slice($arrMask,1);
				}else{ 
					$arrMask=array($this->message['false'],$this->message['true']);
				}
				$this->fieldsArray["$strfieldName"]["select"] = $arrMask;
			}		
			if (substr($mask,0,6)=='select'){ $datatype='select';
				$maskData = array();
				if (strpos($mask,':') >0 ){
					$mask=explode(':',$Mask);
					if (strtoupper(substr($mask[1],0,7)) == 'SELECT ') {					#Select data from Table. Format [SELECT key, value FROM table]
						if ($this->isADO){
							if (($objResult = $this->connectionHandler->Execute($mask[1])) === false)
									$this->SQLerror($mask[1],$this->connectionHandler->ErrorMsg());
							while (!$objResult->EOF){
								$arrResult = $objResult->fields; 
								$maskData[$arrResult[0]]=$arrResult[1];
								$objResult->MoveNext();
							}
						}else{
							$objResult = mysql_query($mask[1]) or $this->SQLerror($mask[1], mysql_error());
							while ($arrResult = mysql_fetch_array($objResult))
								$maskData[$arrResult[0]]=$arrResult[1];
						}
					}else{ 																	#literal select: keyfield must be of the same datatype as the list
						$arrMask=array_slice($mask,1);
						foreach ($arrMask as $ArrData)	{
							$arrOptions = split( '_', $ArrData);
							$rowID = $arrOptions[0];
							if (isset($arrOptions[1])) $rowName = $arrOptions[1]; else $rowName = $rowID;
							$maskData[$rowID]=$rowName;
						}
					}
					$this->fieldsArray["$strfieldName"]["select"] = $maskData;
				}
			}
			$this->fieldsArray["$strfieldName"]["datatype"] = $datatype;
			$this->fieldsArray["$strfieldName"]["inputtype"] = $inputtype; 			  # 0=text 1=readonly 2=hidden, 4=non-field relation Image or Calc
				// numberformats:  with decimals as 0,1,2,3,4, integer, signed, count, percentage, promille, boolean
				// text: gives an input; textarea gives a textarea
				// money as money:sign  i.e money:$ or money:&euro
				// date as date:format:separator i.e. date:dmy:- or date:mdy:/
		}else{
			die("<div id='DG_sqlerror' class='dgError'>".$this->message['doublefield'].":<strong> [$strfieldName]</strong></div>");
		}
	}
	
	# setHeader: Defines the CSS and JS files to be used
	function setHeader($phpScriptFile="", $jsFile = "js/dgscripts.js", $cssFile = "includes/dgstyle.css"){
		if (empty($phpScriptFile)) $phpScriptFile = basename($_SERVER["PHP_SELF"]);
		$sl = ($this->closeTags)?"/":"";						    # This variable must be inserted on each XHTML field which need to be closed with an / (<img ... />).
		$br = ($this->friendlyHTML)?"\n":"";						# This variable must be inserted on each HTML output to format the generated code.
		if (!isset($_REQUEST["DG_ajaxid"])) {
			echo "<link type='text/css' rel='stylesheet' href='$cssFile' $sl>$br";
			echo "<script type='text/javascript' src='$jsFile'></script>$br";
			echo "<script type='text/javascript'>$br".
					"var scrName = '$phpScriptFile';$br".
					"var imgpath = '$this->imgpath';$br".
					"var params = '$this->parameters';$br".
					"var camposearch = '$this->search';$br".
					"var txtDelete = '".$this->message["confirm"]."';$br".
					"var txtSave = '".$this->message["save"]."';$br".
					"var txtCancel = '".$this->message["cancel"]."';$br".
					"var methodForm = '$this->methodForm';$br".
					"var dgAjaxChanged = '$this->dgAjaxChanged';$br".
					"var txtSaving = '".$this->message["saving"]."';$br".
					"var txtLoading = '".$this->message["loading"]."';$br".
					"var thereisCalc = ".$this->hasCalcs.";$br".
					"var decimalPoint = '$this->decimalsep';$br".
					"var decimals = '$this->decimalDigits';$br".
					"var imgSave = '".$this->images["save"]."';$br".
					"var imgCancel = '".$this->images["cancel"]."';$br".
					"var imgAjax = '".$this->images["ajax"]."';$br".
					"function selected_checks(){".$br.
						"var sel_checks = new Array();$br";
		if ($this->checkable){
			echo "var type_elts = typeof(document.forms['$this->FormName'].elements['chksel[]']);$br".
				"var elts_a = ((type_elts) != 'undefined')?document.forms['$this->FormName'].elements['chksel[]']:'';$br".
				"var elts_cnt_a  = (typeof(elts_a.length) != 'undefined')?elts_a.length:0;$br".
				"if (elts_cnt_a) {".$br.
				"	counter_a = 0;$br".
				"	for (var i_a = 0; i_a < elts_cnt_a; i_a++) {".$br.
				"		if ( elts_a[i_a].checked){".$br.
				"			sel_checks[counter_a] = elts_a[i_a].value;$br".
				"			counter_a++;$br".
				"		};$br".
				"	};$br".
				"};$br";
		}
		echo "return sel_checks;$br".
				  "};$br".
				  "</script>$br";
		}
	}
    
    #get the header
    function getHeader($a,$b,$c){
        ob_start();
        $this->setHeader($a,$b,$c);
        $header = ob_get_clean();
        return $header;
    }
	# searchby: Defines the list of fields which can be used to perform searches
	function searchby($listoffields=''){
		$this->search = $listoffields;
	}

	# searchby: Defines the ajax style to edit 'DEFAULT', 'SILENT', '' -> If not ajax edition allowed
	function ajax($style='DEFAULT'){
		$br = ($this->friendlyHTML)?"\n":"";						# This variable must be inserted on each HTML output to format the generated code.
		if (!in_array($style = strtolower($style),array ("default","silent",""))) $style = "";
		$this->ajaxEditable=$style;
		if ($style!=''){
			echo "<script type='text/javaScript'>$br";
			echo "var aColumns= ".$this->PhpArrayToJsObject_Recurse($this->fieldsArray).";$br";
			echo "var ajaxStyle = '$style';$br";
			echo "</script>$br";
		}
	}

	# linkparam: Defines the list of parameters needed to propagate
	function linkparam($param){
		if (substr($param,0,1) != "&") $param = "&".$param;
		$this->parameters = $param;
	}
	
	# selectCombo: Create a Select option pop up to search in :select defined fieldsagate
	function selectCombo($strCampo, $Actual){
		$br = ($this->friendlyHTML)?"\n":"";						# This variable must be inserted on each HTML output to format the generated code.
		$sl = ($this->closeTags)?"/":"";						    # This variable must be inserted on each XHTML field which need to be closed with an / (<img ... />).
		if (in_array($strCampo,explode(",",str_replace(":select","",$this->search)))){
			$strSQL = "SELECT $strCampo FROM ".$this->tablename." GROUP BY $strCampo ORDER BY $strCampo;";
			if ($this->isADO){
				if (($objRes = $this->connectionHandler->Execute($strSQL)) === false)
					$this->SQLerror($strSQL,$this->connectionHandler->ErrorMsg());
			}else{
				$objRes = mysql_query($strSQL, $this->connectionHandler) or $this->SQLerror($strSQL,mysql_error());
			}
			$strSelect = "<select size='1' id='dg_schrstr' class='dgSelectpage' >";
			$exitLoop = false; $conteo = 0;
			do{
				if (!$this->isADO){
					if(!$rowRes = mysql_fetch_array($objRes)) $exitLoop=true;
				}else{
					if (!$objRes->EOF) $rowRes = $objRes->fields; else $exitLoop=true;
				}
				$value = $rowRes[$strCampo];
				if (!$exitLoop and !empty($value)){
					$strSelect.="<option value='$value' ";
					if ($Actual==$value) $strSelect.="selected='selected'";
					$strSelect.=">$value</option>$br"; 
					if ($this->isADO) $objRes->MoveNext();
					$conteo++;
				}
			}while (!$exitLoop);
			$strSelect.="</select>";
			if ($conteo>0){
				echo $strSelect;
			}else{
				echo "<input type='text' id='dg_schrstr' class='input' size='35' value='$Actual' onkeypress='return DG_bl_enter(event)' $sl>$br";
			}
		}else{
			die("<div id='DG_sqlerror' class='dgError'>".$this->message['noinsearch'].":<strong> [$strCampo]</strong></div>");
		}
	}

	# extractLink: Build links in imagelink and in link styles
	function extractLink ($valuelist, $rowRes){
		$valuelist = str_replace("\\,","[comma]",$valuelist);
		if (strpos($valuelist,',')>0){
			$arrAction=explode(',',$valuelist);
			$action=$arrAction[0];
			array_shift($arrAction);
			$i=0; $comma = "";
			foreach ($arrAction as $therow){ 
				$arrAction[$i]= $comma."'".$rowRes[$arrAction[$i]]."'";
				$comma=",";
				$i++;
			}
			$action=vsprintf($action,$arrAction);
		}
		$action=str_replace('"',"'",$action);
		$action=str_replace("'","'",$action);
		$action=str_replace("[comma]",",",$action);
		return $action;
	}

	# putAcutes: Convert characters like á é í ... in the &aacute; equivalent
	function putAcutes($strText){
        if (!$_config['language'] = "russian") {
            $strText = str_replace( "á" , "&aacute;", $strText);
            $strText = str_replace( "é" , "&eacute;", $strText);
            $strText = str_replace( "í" , "&iacute;", $strText);
            $strText = str_replace( "ó" , "&oacute;", $strText);
            $strText = str_replace( "ú" , "&uacute;", $strText);
            $strText = str_replace( "Á" , "&Aacute;", $strText);
            $strText = str_replace( "É" , "&Eacute;", $strText);
            $strText = str_replace( "Í" , "&Iacute;", $strText);
            $strText = str_replace( "Ó" , "&Oacute;", $strText);
            $strText = str_replace( "Ú" , "&Uacute;", $strText);
            $strText = str_replace( "Ñ" , "&Ntilde;", $strText);
            $strText = str_replace( "ñ" , "&ntilde;", $strText);
        }
        return $strText;
    }
	# grid: Main function, draws the table
	function grid(){
		$br = ($this->friendlyHTML)?"\n":"";						# This variable must be inserted on each HTML output to format the generated code.
		$sl = ($this->closeTags)?"/":"";						    # This variable must be inserted on each XHTML field which need to be closed with an / (<img ... />).
		$strNew=""; $strSearch=""; $orderby =""; $where= ""; $orderExpr="";
		
		foreach ($this->fieldsArray as $column){
			$columnName = $column["strfieldName"];
			$mask=$column["mask"];
			if (substr($mask,0,5)=='money' ) {
				$mask = $mask.":".$this->decimalsep;
			}else{
				if (strpos($this->numerics,$mask) > 0) $mask.= ":x:".$this->decimalsep;
			}
			$this->fieldsArray[$columnName]["mask"] = $mask;
		}
		if (empty($this->keyfield) and ($this->delBtn or $this->updBtn or $this->chkBtn or !empty($this->ajaxEditable)))   # If the keyfield is empty and delete, edit, view or ajaxedition is enabled, then not run
			die("<span class='dgError'>You must define the Key Field for transactions like View, Edit, Delete or AJAX edition</span>");
		
		$DG_ajaxid  = (isset($_REQUEST["DG_ajaxid"]))?$_REQUEST["DG_ajaxid"]:0;
		$schrstr = (isset($_REQUEST["dg_schrstr"]))?$_REQUEST["dg_schrstr"]:"";
		$ss      = trim(isset($_REQUEST["dg_ss"]))?$_REQUEST["dg_ss"]:"";

		# Create the where string
		$where = $this->where;
		if (!empty($where)) $where = " WHERE $where";
		if (in_array($ss,explode(",",str_replace(":select","",$this->search))) and !empty($schrstr)){
			$schrstr = $this->GetSQLValueString($schrstr, $this->fieldsArray[$ss]["mask"]);
			$where = ((empty($where))?" WHERE (":$where." AND (")." $ss like '%$schrstr%' )";
		}

		# Create the GROUP BY string
		$groupby = $this->groupby;
		if (!empty($groupby)) $groupby = " GROUP BY $groupby";
		
		# End of where string creation
		switch ($DG_ajaxid) {
			case 2;
				if (!empty($this->search)){
					$fs = (isset($_REQUEST["fs"]))?$_REQUEST["fs"]:"";
					$this->selectCombo($fs,$schrstr);
					die();
				}else{
					die("<div class='dgError'>".$this->message['cannotsearch']."</div>");
				}
			break;
			case 5;
				if ($this->addBtn or $this->updBtn or $this->chkBtn){
					$x=$_REQUEST["x"];
					$y=$_REQUEST["y"];
					echo "<div id='dgAdd$this->FormName' align='center' class='dgAddDiv' style='width:".$x."px; height:".$y."px;";
					if (!strpos($_SERVER['HTTP_USER_AGENT'], "MSIE")===false) echo "filter:alpha(opacity=97);";
					echo "'>&nbsp;";
					$alt = false;
					echo "<table border='1' style='margin-top:30px' cellspacing='0' cellpadding='0'>";
					echo "<tr align='left' class='dgAddTitle'>$br";
					echo "<td colspan='2' align='center'>$br";
					$dtrtd = $_REQUEST["dgrtd"];
					$isadding = (empty($dtrtd))?true:false;
					if ($isadding){
						if (!$this->addBtn){
							die("<div class='dgError'>".$this->message['cannotadd']."</div>");
						}
						$dtrtd = -1;
						$isediting = false;
						$isviewing = false;
						echo $this->message["addRecord"].$br;
					}else{
						$rtd = $_REQUEST["dgrtd"];
						$vcode = $_REQUEST["dgvcode"];
						if (substr($vcode,0,4)=="view"){
							$md = "view".md5($this->salt."ViewRow".$rtd);
							$isediting = false;
							$isviewing = true;
						}else{
							$md = md5($this->salt."EditRow".$rtd);
							$isediting = true;
							$isviewing = false;
						}
						if ($vcode != $md){
							die("<span class='dgError'>".$this->message["errcode"]."</span>");
						}else{
						}
						if (!$this->updBtn and $isediting){
							die("<div class='dgError'>".$this->message['cannotedit']."</div>");
						}
						if (!$this->chkBtn and !$isediting){
							die("<div class='dgError'>".$this->message['cannotedit']."</div>");
						}
						if ($isediting){
							echo $this->message["edtRecord"].$br;
						}else{
							echo $this->message["chkRecord"].$br;
						}
						if (empty($where))
							$updWhere = " WHERE $this->keyfield=".magic_quote($dtrtd)." ";
						else
							$updWhere = str_replace("WHERE", "WHERE ($this->keyfield=".magic_quote($dtrtd).") and ",$where);

						$strSelect = "SELECT ".implode(",",$this->getFields("0,1,2"))." FROM $this->tablename $updWhere LIMIT 1";
						if ($this->isADO){
							if (($objRes = $this->connectionHandler->Execute($strSelect)) === false)
								$this->SQLerror($strSelect,$this->connectionHandler->ErrorMsg());
						}else{
							$objRes = mysql_query($strSelect, $this->connectionHandler) or $this->SQLerror($strSelect,mysql_error());
						}
						if (!$this->isADO){
							$rowRes = mysql_fetch_array($objRes);
						}else{
							if (!$objRes->EOF) $rowRes = $objRes->fields; 
						}
					}
					echo "</td>$br</tr>$br";
					$hiddenFl = "";
					$campos = array();
					
                    foreach ($this->getFields("0,1,2") as $value){
                        $clAlt = ($alt)?"alt":"norm";

                        $dataType   = $this->fieldsArray[$value]['datatype'];
                        $isreadonly =($this->fieldsArray[$value]['inputtype']==1)?true:false;
                        $mask       = $this->fieldsArray[$value]['mask'];
                        $ishidden   =($this->fieldsArray[$value]['inputtype']==2)?true:false;

                        $fldLengt   = $this->fieldsArray[$value]['maxlength'];
                        $fldname    = $this->fieldsArray[$value]['strfieldName'];
                        $selData    = $this->fieldsArray[$value]['select'];
                        $strHeader  = $this->fieldsArray[$value]['strHeader'];
                        if ($isadding)
                            $default= $this->fieldsArray[$value]['default'];
                        else
                            $default= $this->mask($rowRes[$value],$mask,$dataType,$selData,$rowRes);
                        $class = "dgRows".$clAlt."TR";
                        $strInput = "<tr align='left' class='$class'>$br";
                        $strInput.= "<td class='dgAddNames' >$strHeader</td>$br";
                        $strInput.= "<td class='dgAddInputs'>";
                        if ($isreadonly or $ishidden or $isviewing){
                            $fldData = "<input id='$fldname' type='hidden' value='$default' $sl>$br";
                            $hiddenFl.=$fldData;
                            if ($isreadonly or $isviewing) $strInput.=$default;
                            if ($ishidden) $strInput = "";
                            $campos[] = $fldname;
                        }else{
                            switch ($dataType){
                                case 'image': case 'imagelink': case 'link': case 'calc': case 'chart':
                                    $strInput="";
                                    break;
                                case 'select':
                                    $strInput.= "<select id='$fldname' class='dgSelectpage' >$br";
                                    foreach ($selData as $key=>$value){
                                        $selected=($value==$default)?"selected":"";
                                        $strInput.= "<option value='$key' $selected. >$value</option>$br";
                                    }
                                    $strInput.= "</select>";	
                                    $campos[] = $fldname;
                                    break;									
                                    case "check":
                                        $checked=($default==$this->fieldsArray[$value]["select"][1])?"checked":"";
                                    $strInput.= $this->fieldsArray[$value]["select"][0]."/".$this->fieldsArray[$value]["select"][1]."$br";
                                    $strInput.= "<input id='$fldname' type='checkbox' $checked class='dgCheck'>$br";
                                    $campos[] = $fldname.":check";
                                    break;
                                    case "textarea":
                                        $strInput.= "<textarea id='$fldname' class'input' maxlength='$fldLengt' rows='".$this->fieldsArray[$value]["fieldWidth"]."' >$default</textarea>$br";
                                    $campos[] = $fldname;
                                    break;
                                default:
                                    $strInput.= "<input id='$fldname' type='text' class='input' value='$default' maxlength='$fldLengt' $sl>";
                                    $campos[] = $fldname;
                                    break;
                            }
                        }
                        if ($strInput!="") $strInput.= "</td>$br</tr>$br";
                        echo $strInput;
                        $alt = !$alt;
                    }
                    echo "<tr class='dgAddButons'>$br";
                    echo "<td colspan='2' align='center'>$br";
                    echo "$hiddenFl$br";
                    $strArrFields = "arrFields = new Array(\"".implode("\",\"",$campos)."\")";
                    if ($isediting or $isadding){
                        echo "<input type='button' value='".$this->message["save"]."' class='dgInput' onclick='$strArrFields;DG_doSave(arrFields,\"$dtrtd\")' $sl>$br";
                        echo "<input type='button' value='".$this->message["cancel"]."' class='dgInput' onclick='DG_sii(\"addDiv\",\"\");' $sl>$br";
                    }else{
                        echo "<input type='button' value='".$this->message["close"]."' class='dgInput' onclick='{$this->actionCloseDiv}' $sl>$br";
                    }
                    echo "</td>$br</tr>$br";
                    echo "</table></div>";
                    die();
                }else{
                    die("<div class='dgError'>".$this->message['cannotadd']."</div>");
                }
                break;
        }

		# Create the orderby string
		$order = $this->orderColName;
		if (isset($_REQUEST["dg_order"])) if ( $this->validField( $_REQUEST["dg_order"]) and !empty($_REQUEST["dg_order"])) $order = $_REQUEST["dg_order"];
		if (!empty($order)){
			$orderby   = " ORDER BY ".$order;
			$orderExpr = $this->orderExpr;
			if (isset($_REQUEST["dg_oe"])) $orderExpr = strtoupper($_REQUEST["dg_oe"]); 
			if ($orderExpr=="ASC" or $orderExpr=="DESC")
				$orderby.= " $orderExpr";
		}
		# End of order by string creation
		
		# Define the number for the initial record to show
		$recno = $this->recno;
		if (isset($_REQUEST["dg_r"])) $recno = $_REQUEST["dg_r"];
		$recno = intval($recno);
	
		if (!isset($_REQUEST["DG_ajaxid"])) {
			if ($this->doForm)	 # if true then generate <form> and </form> Tags
				echo "$br<form method='".strtolower($this->methodForm)."' action='".basename($_SERVER["PHP_SELF"])."' id='$this->FormName' >$br"; 
				
			echo "<!-- Powered by phpMyDataGrid - Version: ".$this->dgVersion."-->$br";
			# Draw the search box
			if (!empty($this->search)){
				echo "<div id='DG_srchDIV' align='center' class='dgSearchDiv'>$br";
				echo "<span class='dgSearchTit' onmousedown='DG_clickCapa(event, this)' onmouseup='DG_liberaCapa()'>".$this->message["search"]."</span>$br";
				echo "<img style='cursor:pointer; float:right' src='".$this->imgpath.$this->images["close"]."' alt='[X]' width='16' height='16' onclick='DG_hss(\"DG_srchDIV\",\"none\")' $sl>$br";
				echo "<div id='DG_subdiv' class='dgInnerDiv' align='center'>$br";
				echo "<br $sl><br $sl><select size='1' id='dg_ss' class='dgSelectpage' ";
				if (substr_count($this->search, ':')>0) echo "onchange='DG_setsearch(this.value,\"$schrstr\")'";
				echo ">$br";
				$fields4search=explode(",",$this->search);
				$selectFields = ""; $ActualIsSelect=0;
				foreach ($fields4search as $FldOption){
		   	    	if ($hasSelect= (substr_count($FldOption, ':')>0)){
						$ActualIsSelect=(($FldOption = trim(str_replace(":select", "", $FldOption)))==$ss);
					}
					foreach ($this->fieldsArray as $column){
						if($column["strfieldName"]==$FldOption){
							if ($hasSelect){
								$coma=(!empty($selectFields) and !empty($column["strfieldName"]))?",":"";
								$selectFields.=$coma.$column["strfieldName"];
							}
							echo "<option value='".$column["strfieldName"]."' ";
							if ($ss==$column["strfieldName"]) echo "selected";
							echo ">".$column["strHeader"]."</option>$br";
						}
					}
				}
				echo "</select><br $sl><br $sl>$br<span id='searchBox'>$br";
				echo "<input type='hidden' id='boxshr' value='0' $sl>$br";
				if ($ActualIsSelect){
					$this->selectCombo($ss,$schrstr);
				}else{
					echo "<input type='text' id='dg_schrstr' class='input' size='35' value='$schrstr' onkeypress='return DG_bl_enter(event)' $sl>$br";
				}
				$display = (empty($schrstr))?"none":"inline";
				echo "</span><img  id='imgsearch' src='".$this->imgpath.$this->images["view"]."' width='16' height='16' alt='".$this->message['search']."' class='dgImgLink' onclick='DG_doSearch();' $sl>$br";
				echo "<br $sl><br $sl><span id='rstsearch' style='display:$display'>$br";
				echo "<a href='javascript:DG_resetSearch();' class='dgBold' >".$this->message['resetSearch']."</a></span>$br";
				echo "</div></div>$br";
			}else{
				echo "<input type='hidden' id='dg_ss' value='' $sl>$br";
				echo "<input type='hidden' id='dg_schrstr' value='' $sl>$br";
			}
			#End searchBox
			echo "<div id='ajaxDHTMLDiv' style='display:inline;position:absolute;'></div>$br";
			echo "<div id='addDiv' class='dgAddDiv' style='display:inline;position:absolute;'></div>$br";
			echo "<div id='dgDiv' class='dgMainDiv'>$br"; 
		}
		
		# Build SELECT SQL String to count total of records
		$pagSelect = "SELECT count(*) FROM $this->tablename $where $groupby $orderby";
		$fltypes = "0,1,3,4,5";
		#Draw the Grid
		echo "<table id='dgTable' class='dgTable' cellpadding='0' cellspacing='0' >$br";
		$strHeader = "<tr align='center'>$br"; $fieldsCount = 0;
		if ($this->checkable) {
			$strHeader.="<td class='dgTitles' style='width:16px'>$br";
			$strHeader.="<div class='checkbox' onclick='DG_setCheckboxes(\"$this->FormName\", this.checked)'>$br";
			$strHeader.="<input type='checkbox' $sl>$br";
			$strHeader.="</div>$br</td>$br";
		}
		foreach ($this->getFields($fltypes) as $fldName){					# Show table headers
			$strHeader.="<td class='dgTitles'>";
			if ($this->orderArrows and !in_array($this->fieldsArray[$fldName]['inputtype'], array(2,3,4))){
				$strHeader.="<table border='0' cellspacing='0' cellpadding='0' style='width:100%'>$br";
				$strHeader.="<tr>$br<td class='dgArrows' style='vertical-align:bottom'>$br";
				$strHeader.="<img src='".$this->imgpath.$this->images["miniup"]."' class='dgImgLink' alt='^' onClick=\"DG_orderby('".$this->fieldsArray[$fldName]["strfieldName"]."','ASC')\" $sl></td>$br";
				$strHeader.="<th rowspan='2' style='vertical-align:middle;' align='center'>$br";
			}
			$strHeader.=$this->putAcutes($this->fieldsArray[$fldName]['strHeader']).$br;
			if ($order == $this->fieldsArray[$fldName]['strfieldName']){
				if (!empty($orderExpr)){
					$arrowName = $this->imgpath.$this->images["$orderExpr"];
					if (!$this->file_exists_incpath($arrowName)) $strHeader.= "<img src='$arrowName' alt='$orderExpr' width='10' height='10' $sl>$br";
				}
			}
			if ($this->orderArrows and !in_array($this->fieldsArray[$fldName]['inputtype'], array(2,3,4))){
				$strHeader.="</th>$br</tr>$br";
				$strHeader.="<tr>$br<td class='dgArrows' style='vertical-align:top'>$br";
				$strHeader.="<img src='".$this->imgpath.$this->images["minidown"]."' class='dgImgLink' alt='v' onClick=\"DG_orderby('".$this->fieldsArray[$fldName]["strfieldName"]."','DESC')\" $sl></td>$br</tr>$br</table>$br";
			}
			$strHeader.= "</td>$br";
			$fieldsCount++;
		}
		$widthtop = 0;
		if ($this->addBtn) $widthtop+= 22;
		if (!empty($this->search)) $widthtop+= 22;
		
		$widthbottom = 0;
		if ($this->chkBtn) $widthbottom+= 22;
		if ($this->updBtn) $widthbottom+= 22;
		if ($this->delBtn) $widthbottom+= 22;
		
		$width = ($widthtop>$widthbottom)?$widthtop:$widthbottom;
		if ($width != 0){
			# Add cell to header for buttons add & Search
			$strHeader.= "<td align='right' class='dgTitles' style='width:".$width."px'><div style='width:".$width."px'>";
			$strNew    = ($this->addBtn)?"<img src='".$this->imgpath.$this->images["add"]."' alt='".$this->message['add']."' class='dgImgLink' onclick='$this->addonClic' $sl>":"";
			$strSearch = (!empty($this->search))?"<img src='".$this->imgpath.$this->images["search"]."' alt='".$this->message['search']."' class='dgImgLink' onclick='$this->srconClic' $sl>":"";
			$strHeader.= $strNew . $strSearch."</div></td>$br</tr>$br";
			$colsToAdd = 1;
		}else{
			$colsToAdd = 0;
		}
		if ($this->checkable) $colsToAdd++;
		
		if (!empty($this->titulo))
			$strHeader = "<tr align='center'><td colspan='".($fieldsCount+$colsToAdd)."' class='dgHeader'>$this->titulo</td></tr>$br" . $strHeader;
		echo $strHeader;
		
		if ($this->isADO){
			if (($objSQLpag = $this->connectionHandler->Execute($pagSelect)) === false)
				$this->SQLerror($pagSelect,$this->connectionHandler->ErrorMsg());
			else
				if (empty($groupby))
					$intRecords = $objSQLpag->fields[0];			
				else 
					$intRecords = $objSQLpag->RecordCount();			
		}else{
			$objSQLpag  = mysql_query($pagSelect, $this->connectionHandler) or $this->SQLerror($pagSelect,mysql_error());
			$arrRow 	= mysql_fetch_array($objSQLpag);
			if (empty($groupby))
				$intRecords = $arrRow[0];
			else 
				$intRecords = mysql_num_rows($objSQLpag);
		}
		$recno = ($recno>$intRecords)?$intRecords:$recno;
		# Build SELECT SQL String to count total of records

		$arrSQLFld = $this->getFields("0,1,4,5");
		if (!empty($this->keyfield) and !in_array($this->keyfield,$arrSQLFld)) $arrSQLFld[] = $this->keyfield;
		
		$sqlFields = implode(",", $arrSQLFld);

		if ($this->hasChart){  // If chart fields are defined
			$arrChart = array();
			$maxValues = array();
			if (empty($this->sql)) 
				$strSelect = "SELECT $sqlFields FROM $this->tablename $where $groupby $orderby LIMIT $recno, $this->maxRec";
			else
				$strSelect = $this->sql." $where $orderby $orderby LIMIT $recno, $this->maxRec";
			$exitLoop = false;
			$chartColor = 0;
			if ($this->isADO){
				if (($objRes = $this->connectionHandler->Execute($strSelect)) === false)
					$this->SQLerror($strSelect,$this->connectionHandler->ErrorMsg());
			}else{
				$objRes = mysql_query($strSelect, $this->connectionHandler) or $this->SQLerror($strSelect,mysql_error());
			}
			do{
				if (!$this->isADO){
					if(!$rowRes = mysql_fetch_array($objRes)) $exitLoop=true;
				}else{
					if (!$objRes->EOF) $rowRes = $objRes->fields; else $exitLoop=true;
				}
				if (!$exitLoop){
					$keyValue = $rowRes[$this->keyfield];
					foreach ($this->getFields("5") as $key=>$value){
						$dataType   = $this->fieldsArray[$value]['datatype'];
						$fldname    = $this->fieldsArray[$value]['strfieldName'];
						$select     = $this->fieldsArray[$value]['select'];
						$rowValue = $rowRes[$value]; 
						if ($dataType=="chart") {
							$arrChart[$fldname][$keyValue] = $rowValue;
							$arrChart[$fldname][$keyValue."c"] = $this->color4Charts[$chartColor];
							$arrChart[$fldname][$keyValue."s"] = $this->fieldsArray[$value]['select'];
							$maxValue = (isset($select[1]))?$select[1]:"max";
							switch ($maxValue){
								case "sum" : 
									$actualValue = (isset($maxValues[$fldname]))?$maxValues[$fldname]:0;
									$actualValue+= $rowValue;
									$maxValues[$fldname] = $actualValue;
								break;
								case "max" :
									$actualValue = (isset($maxValues[$fldname]))?$maxValues[$fldname]:0;
									if ($rowValue > $actualValue) 
										$maxValues[$fldname] = $rowValue;
									else
										$maxValues[$fldname] = $actualValue;
								break;
								case "val" : 
									$maxValues[$fldname] = $select[2];
							}
							$chartColor++; if ($chartColor>70) $chartColor=0;
						}
					}
					if ($this->isADO) $objRes->MoveNext();
				}
			}while (!$exitLoop);
		}
		if (empty($this->sql)) 
			$strSelect = "SELECT $sqlFields FROM $this->tablename $where $groupby $orderby LIMIT $recno, $this->maxRec";
		else
			$strSelect = $this->sql." $where $groupby $orderby LIMIT $recno, $this->maxRec";
		if ($this->isADO){
			if (($objRes = $this->connectionHandler->Execute($strSelect)) === false)
				$this->SQLerror($strSelect,$this->connectionHandler->ErrorMsg());
		}else{
			$objRes = mysql_query($strSelect, $this->connectionHandler) or $this->SQLerror($strSelect,mysql_error());
		}
		$paginas = ceil(($intRecords/$this->maxRec));
		## Begin of process to draw rows
		$exitLoop = false;
		$alt = false; $countRecords = 0;
		$totalColumn = array();
		$keyTemp = 0;
		do{
			if (!$this->isADO){
				if(!$rowRes = mysql_fetch_array($objRes)) $exitLoop=true;
			}else{
				if (!$objRes->EOF) $rowRes = $objRes->fields; else $exitLoop=true;
			}
			if (!$exitLoop){
				if (empty($this->keyfield)){
					$keyValue = $keyTemp; $keyTemp++;
				}else
					$keyValue = $rowRes[$this->keyfield];
					
				$countRecords++;
				$clAlt = ($alt)?"alt":"norm";
				echo "<tr class='dgRows".$clAlt."TR'>$br";

				if ($this->checkable){
					echo "<td align='center' class='dgRow$clAlt' >$br";
					echo "<div class='checkbox'>$br";
					echo "<input type='checkbox' name='chksel[]' value='$keyValue' $sl></div>$br</td>$br";
				}

				foreach ($this->getFields($fltypes) as $key=>$value){
					$dataType   = $this->fieldsArray[$value]['datatype'];
					$isreadonly =($this->fieldsArray[$value]['inputtype']==1)?true:false;
					$mask       = $this->fieldsArray[$value]['mask'];
					$fldLengt   = $this->fieldsArray[$value]['maxlength'];
					$columnwidth= $this->fieldsArray[$value]['columnwidth'];
					$fldAlign   = $this->fieldsArray[$value]['align'];
					$fldname    = $this->fieldsArray[$value]['strfieldName'];
					$selData    = $this->fieldsArray[$value]['select'];
					$strHeader  = $this->fieldsArray[$value]['strHeader'];
					$cutChar    = $this->fieldsArray[$value]['cutChar'];
					echo "<td class='dgRow$clAlt' align='$fldAlign'>$br";
					echo "<div id='$value.-.$keyValue' style='width:$columnwidth;' ";

					if (empty($this->ajaxEditable) or $isreadonly or in_array($dataType,array("link","image","imagelink","calc","chart"))){
						if ($dataType=='link') echo "class='dgLinks'"; 
						if ($dataType=='calc') echo "class='dgBold'";
					}else{
						echo "onclick='DG_D_edit(this,\"".md5($this->salt.$value.":toEdit:".$keyValue)."\")' ";
					}
					echo ">$br";
					$rowValue = (isset($rowRes[$value]))?$this->putAcutes($rowRes[$value]):""; 
					$rowValue = ($rowValue=="" or is_null($rowValue))?"&nbsp;":$rowValue;
					if (in_array($fldname, $this->totalize)){
						if (isset($totalColumn[$fldname]))
							$totalColumn[$fldname]+= $rowValue;
						else
							$totalColumn[$fldname] = $rowValue;
					}
					switch ($dataType){
						case 'image': case 'imagelink':
							if ($dataType=="imagelink"){
								list( $type, $imagedata, $valuelist) = split( ':', $mask);
							}else{
								list( $type, $imagedata) = split( ':', $mask);
							}
							if (!empty($imagedata)) $value=str_replace("%s",str_replace("&nbsp;","",$rowValue),$imagedata);
							if (file_exists($value)){
								$strHeader=DGXtract($strHeader,"<em>","</em>");
								echo "<img id='icn_{$fldname}.-.{$keyValue}' alt='$strHeader' src='$value' ";
								if($dataType=='imagelink'){
									echo 'class="dgImgLink" onclick="'.$this->extractLink ($valuelist, $rowRes).'"';
								}
								echo " $sl>$br";
							}else{
								echo "File not found: $value";
							}
						break;
						case 'link': 
							list( $type, $valuelist) = split( ':', $mask);
							echo "<a href=\"javascript:";
							echo $this->extractLink ($valuelist, $rowRes);
							echo "\">$rowValue</a>$br";
						break;
						case 'calc':
							list( $type, $e) = split( ':', $mask); $eTC=$e;
							$e=str_replace("+"," ",$e);$e=str_replace("-"," ",$e);$e=str_replace("/"," ",$e);
							$e=str_replace("*"," ",$e);$e=str_replace("("," ",$e);$e=str_replace(")"," ",$e);
							$eTC=str_replace("+"," + ",$eTC);$eTC=str_replace("-"," - ",$eTC);
							$eTC=str_replace("/"," / ",$eTC);$eTC=str_replace("*"," * ",$eTC);
							$eTC=str_replace("("," ( ",$eTC);$eTC=str_replace(")"," ) ",$eTC);
							$varExpresion = explode(' ',$e);
							foreach ($varExpresion as $Field){
								$vrField = (empty($rowRes[$Field]))?0:$rowRes[$Field]; $mayDo= 0;
								foreach ($this->getFields() as $i){
									$vartmp = $this->fieldsArray[$i]["strfieldName"];
									if($vartmp==$Field) $mayDo=1;
								}
								if ($mayDo==1) $eTC=str_replace(" ".$Field." ",$vrField, $eTC);
							}
							eval("echo number_format(".$eTC.",$this->decimalDigits);");
						break;
						case 'chart':
							$percentChart = round((( $arrChart[$fldname][$keyValue] / $maxValues[$fldname] ) * 100),2); 
							$vts = $arrChart[$fldname][$keyValue."s"];
							switch ($vts[0]){
								case "percent": $valuetoShow = "$percentChart%"; break;
								case "value" : $valuetoShow = $this->mask($arrChart[$fldname][$keyValue],2,"number","",$rowRes); break;
								default : $valuetoShow = "&nbsp;"; break;
							}
							echo "<div style='width:".$percentChart."%; background:".$arrChart[$fldname][$keyValue."c"]."' >".$valuetoShow."</div>$br";
						break;
						default:
							$rowValue = trim($rowValue); $rowValue = (empty($rowValue))?"&nbsp;":$rowValue;
							if ($cutChar>0 and strlen($rowValue)>$cutChar) $rowValue = substr($rowValue,0,$cutChar)."...";
							echo $this->mask($rowValue,$mask,$dataType,$selData,$rowRes);
						break;
					}
					echo "</div>$br";
					echo "</td>$br";
				}
				if ($width != 0){
					echo "<td align='center' class='dgRow$clAlt'>";
					if ($this->chkBtn) printf ("<img src='".$this->imgpath.$this->images["view"]."' alt='".$this->message['view']."' class='dgImgLink' onclick='".$this->vieonClic."' $sl>",$keyValue,md5($this->salt."ViewRow".$keyValue));
					if ($this->updBtn) printf ("<img src='".$this->imgpath.$this->images["edit"]."' alt='".$this->message['edit']."' class='dgImgLink' onclick='".$this->edtonClic."' $sl>",$keyValue,md5($this->salt."EditRow".$keyValue));
					if ($this->delBtn) printf ("<img src='".$this->imgpath.$this->images["erase"]."' alt='".$this->message['delete']."' class='dgImgLink' onclick='".$this->delonClic."' $sl>",$keyValue,md5($this->salt."Delete".$keyValue));
					echo "</td>$br</tr>$br";
				}
				$alt = !$alt;
				if ($this->isADO) $objRes->MoveNext();
			}
		}while (!$exitLoop);
		if ($countRecords==0){
			echo "<tr align='center'><td colspan='".($fieldsCount+$colsToAdd)."' class='dgError'>$br<strong>";
			echo $this->message['norecords'];
			echo "</strong></td>$br</tr>$br";
		}else{
			if (!empty( $this->totalize)){
				echo "<tr class='dgTotRowsTR'>$br";
				if ($this->checkable) echo "<td class='dgRowsTot'>&nbsp;</td>";
				foreach ($this->getFields($fltypes) as $key=>$value){
					$dataType   = $this->fieldsArray[$value]['datatype'];
					$mask       = $this->fieldsArray[$value]['mask'];
					$fldAlign   = $this->fieldsArray[$value]['align'];
					$fldname    = $this->fieldsArray[$value]['strfieldName'];
					echo "<td align='$fldAlign' class='dgRowsTot'>$br";
					if (in_array($fldname, $this->totalize))
						echo $this->mask($totalColumn[$fldname],$mask,$dataType,"",array());
					else
						echo "&nbsp;";
					echo "</td>$br";
				}
				if ($width != 0){
					echo "<td colspan='".($colsToAdd)."' align='right' class='dgRowsTot'>".$strNew.$strSearch."&nbsp;</td>$br</tr>$br";
					$strNew = "";
					$strSearch = "&nbsp;";
				}
			}		
		}
		## Fin del proceso principal
		echo "<tr align='center'><td align='left' colspan='".($fieldsCount+($colsToAdd-1))."' class='dgPagRow'>$br";
		# Begin of Pagination Module
		if ($paginas>1) {				
			$pm = $this->pagination;
			$pinto=0; $pActual=-9999;
			for ($conteoPag=0; $conteoPag < $paginas; $conteoPag++)
				if (($recno>=$conteoPag * $this->maxRec) and ($recno < ( $conteoPag + 1 ) * $this->maxRec)) $pActual = $conteoPag;
			$pAnterior  = (($pActual - 1<0)?0:$pActual - 1)*$this->maxRec;
			$pSiguiente = (($pActual + 1>$paginas)?$paginas:$pActual + 1)*$this->maxRec;
			$imgTop = (($recno - $this->maxRec) < 0)?'_off':'';
			$imgBot = (($recno + $this->maxRec) >= $intRecords)?'_off':'';
			echo "<img class='dgImgLink' src='".$this->imgpath.$this->images["up$imgTop"]."' alt='".$this->message['prev']."' ";
			if ($imgTop!="_off") echo " onclick='DG_chgpg($pAnterior)'";
			echo " $sl>&nbsp;$br";
			for ($conteoPag=0; $conteoPag < $paginas; $conteoPag++){
				$newinicial = $conteoPag * $this->maxRec;
				if ($pm == 'links' or $pm=='mixed'){
					$dgLA = $this->linksperpage;
					if ((($conteoPag > $pActual - ($dgLA + 1)) and ($conteoPag <= $pActual + $dgLA)) or ($conteoPag < $dgLA or $conteoPag>= $paginas - $dgLA)){
						if ($conteoPag==$pActual) {
							$strLink = "class='dgBold'"; $prn=0;
						}else{
							$strLink = "class='dgLinks' onclick='DG_chgpg($newinicial)'"; $prn=1;
						}
						if ($prn == 1 or ($prn==0 and $pm=='links'))
							echo "<span $strLink>".($conteoPag+1)."</span>&nbsp;$br";
						$pinto=0;
					}else{
						if ($pinto==0){
							echo "...&nbsp;&nbsp;$br";
							$pinto=1;
						}
					}
				}
				if (($pm == 'select' or $pm=='mixed') and $conteoPag==$pActual){
					echo "<select class='dgSelectpage' name='pages' size='1' onchange='DG_chgpg(this.value);' >$br";
					for ($conteoSelect=0; $conteoSelect < $paginas; $conteoSelect++){
						$newinselect = ($conteoSelect ) * $this->maxRec;
						echo '<option ';
						if ($conteoSelect==$pActual) {
							echo 'selected ';
						}
						echo "value='$newinselect'>".($conteoSelect+1)."</option>$br";
					}
					echo "</select>&nbsp;$br";
				}
			}
			echo "<img class='dgImgLink' src='".$this->imgpath.$this->images["down$imgBot"]."' alt='".$this->message['next']."'  ";
			if ($imgBot!="_off") echo " onclick='DG_chgpg($pSiguiente)'";
			echo " $sl>&nbsp;<br $sl>$br";
		}	
		# End of Pagination Module
		if ($intRecords>0 and $this->showToOf) echo $this->message['display']." ".($recno+1)." ".$this->message['to']." ".($recno+$countRecords)." ".$this->message['of']." $intRecords<br $sl>$br";
		echo "</td>$br";

		echo "<td class='dgPagRow' align='right'>".$strNew.$strSearch."&nbsp;</td>$br</tr>$br";
		
		if (!empty($this->footer))
			echo "<tr align='center'><td colspan='".($fieldsCount+$colsToAdd)."' class='dgFooter'>$this->footer</td></tr>$br";
		
		if (!empty($this->poweredby))
			echo "<tr align='center'><td colspan='".($fieldsCount+$colsToAdd)."'><br $sl><a href='http://www.gurusistemas.com' target='_blank'><img src='".$this->imgpath."poweredby.png' alt='Powered by phpMyDataGrid' border='0' $sl></a><br $sl></td></tr>$br";
		echo "</table>$br";

		# Control fields
		echo "<input type='hidden' id='dg_r' value='$recno' $sl>$br";
		echo "<input type='hidden' id='dg_order' value='$order' $sl>$br";
		echo "<input type='hidden' id='dg_oe' value='$orderExpr' $sl>$br";
		echo "<input type='hidden' id='ajaxDHTMLediting' value='0' $sl>$br"; 

		if (!isset($_REQUEST["DG_ajaxid"])) echo "</div>$br";
		if (!isset($_REQUEST["DG_ajaxid"]) and $this->doForm)	 # if true then generate <form> and </form> Tags
			echo "</form>$br"; 
		if (isset($_REQUEST["DG_ajaxid"])) die();
	}

	function mask($value,$mask,$datatype,$aselect,$row){
		switch ($datatype){
			case 'number': 
				return $this->number_mask($value,$mask);
			break;
			case 'date':
				return $this->date_mask($value,$mask);
			break;
			case 'check':
				if (strpos($mask,':')>0){
					$arrMask=explode(':',$mask);
					$value=(empty($value))?0:$value;
					return  $arrMask[$value+1];
				}
			break;
			case 'select':
				if (is_array($aselect) && !empty($value) && isset($aselect[$value])) return $aselect[$value]; else return $value;
			break;
			default:		
				return $value;
		}
	}
	
	function number_mask($value,$mask){
		if (is_null($value || is_numeric($value)==false )) return $value;
		$decimalsep = $this->decimalsep; 
		$moneySign='';
        $sign ='';
		if (strpos($mask,':')>0){
			$arrMask=explode(':',$mask);
			$mask=$arrMask[0]; 
			$sign  = (empty($arrMask[1])) ? $sign:$arrMask[1];
            $moneySign  = (empty($arrMask[1])) ? $moneySign:$arrMask[1];
			$decimalsep = (empty($arrMask[2])) ? $decimalsep:$arrMask[2];
		}
		$thousandsep= ($decimalsep=='.') ? ',': '.';
		switch ($mask){
			case '0': case '1': case '2': case '3': case '4': $retval=sprintf ('%s', number_format($value,$mask,$decimalsep,$thousandsep)); break; 
			case 'money': $retval=sprintf ('%s  %s', $moneySign, number_format($value,$this->decimalDigits,$decimalsep,$thousandsep)) ; break; 
			case 'sign': $retval=sprintf ('%s  %s',  number_format($value ,2,$decimalsep,$thousandsep), $sign) ; break; 
		    case 'count': case 'integer': case 'unsigned': $retval=sprintf ('%s', number_format($value,0,$decimalsep,$thousandsep)) ; break; 
			case 'percentage':$value=$value*100; $retval=sprintf ('%s ', number_format($value,$this->decimalDigits,$decimalsep,$thousandsep)).'%' ; break; 
			case 'promille': $value=$value*1000;$retval=sprintf ('%s &permil;', number_format($value,$this->decimalDigits,$decimalsep,$thousandsep)) ; break; 
			default: $retval= number_format($value ,2,$decimalsep,$thousandsep); break;
		} 
		return $retval; 
	}

	function date_mask($value,$mask){	
		if($value != "") {
			$format='';
			$separator='';
			if (strpos($mask,':')>0){
			 	$arrMask=explode(':',$mask);
				$theType=$arrMask[0]; 
				$format=(empty($arrMask[1])) ? $format : $arrMask[1];
				$separator=(empty($arrMask[2])) ? $separator: $arrMask[2];
			}
			$arrDdate = $this->datecheck($value,'ymd','-', $format, $separator);
			if ($arrDdate != false)	$value =$arrDdate['todate'] ;
		} 
		return  $value;
	}

	function datecheck($date,$format='ymd',$separator='-',$toformat='mdy',$toseparator='-') {
		$format = ($format=='')?'ymd':strtolower($format);
		if (count($datebits=explode($separator,$date))!=3) return false;
		$year = intval($datebits[strpos($format, 'y')]);
		$month = intval($datebits[strpos($format, 'm')]);
		$day = intval($datebits[strpos($format, 'd')]);
		$year=($year <10 )? '200'.$year:$year;
		$year=($year <50 )? '20' .$year:$year;
		$year=($year <100)? '19' .$year:$year;
		$month=($month <10)? '0' .$month:$month;
		$day=($day <10)? '0' .$day:$day;
		if (($month<1) || ($month>12) || ($day<1) || (($month==2) && ($day>28+(!($year%4))-(!($year%100))+(!($year%400)))) || ($day>30+(($month>7)^($month&1)))) return false; // date out of range 
		$arrDate= array('y' => $year, 'm' => $month, 'd' => $day, 'iso' => $year.'-'.$month.'-'.$day, 'fromdate'=> $date, 'todate' => '' );
		$arrDate['todate'] = $arrDate[$toformat[0]].$toseparator.$arrDate[$toformat[1]].$toseparator.$arrDate[$toformat[2]];
		return $arrDate;
	}

	function PhpArrayToJsObject_Recurse($array){   
		if(! is_array($array) ){
			if ($array === null) return null;
			return '"' . $array . '"';
		}
		$retVal = "{"; $first = true;
		foreach($array as $key => $value){
			if (! $first ) $retVal .= ', '; $first = false;
			if (is_string($key)) $key = "\"$key\"";
			$retVal .= $key . " : " . $this->PhpArrayToJsObject_Recurse($value);
		}
		return $retVal . "}";
	}

	function GetSQLValueString($theValue, $theType, $theDefinedValue = 1, $theNotDefinedValue = 0) {
		$theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;
		$format='';
		$separator='.';
		$thousandsep=',';
		if (strpos($theType,':')>0){
		 	$arrMask=explode(':',$theType);
			$theType=$arrMask[0]; 
			$format=(empty($arrMask[1])) ? $format: $arrMask[1];
			$separator=(empty($arrMask[2])) ? $separator: $arrMask[2];
			$thousandsep= ($separator=='.') ? ',': '.';
		}
		switch ($theType) {
			case "textarea": case "text": 
				$theValue = (!empty($theValue)) ? $theValue : ""; 
			break; 
			case "0" : case "signed" : case 'count' : case "integer": 
				if ($theValue == "") { 
					$theValue = 0;
				}else{
					$theValue = str_replace($thousandsep, '', $theValue);    
					$theValue = str_replace($separator, '.', $theValue);    
					$theValue = intval($theValue) ;
				}
			break;
			case "money": 
				$Value= $theValue;
				while (!is_numeric(substr($Value,0,1))) $Value= trim(substr($Value,1,20));
				$theValue = $Value;
			case "1" : case "2" : case "3" : case "4" : case "float" : case "double":
				if ($theValue == "") {
					$theValue = 0;
				}else{
					$theValue = str_replace($thousandsep, '', $theValue);    
					$theValue = str_replace($separator, '.', $theValue);  
					$theValue = floatval($theValue) ; 
				}
			break;
			case "percentage":
				if ($theValue == "") {
					$theValue = 0;
				}else{
					$theValue = trim(str_replace('%', '', $theValue));
					$theValue = str_replace($thousandsep, '', $theValue);    
					$theValue = str_replace($separator, '.', $theValue);    
					$theValue = floatval($theValue)/100 ;}
			break;
			case "promille":
				if ($theValue == "") {
					$theValue = 0;
				}else{
					$theValue = trim(str_replace('‰', '', $theValue));
					$theValue = str_replace($thousandsep, '', $theValue);    
					$theValue = str_replace($separator, '.', $theValue);    
					$theValue = floatval($theValue)/1000 ;
				}
			break;
			case "date": 
				if($theValue != "") {
					$adate = $this->datecheck($theValue,$format,$separator);
					$theValue = ($adate != false) ? $adate['iso'] : "0000-00-00";
				}else{
					$theValue ="0000-00-00";
				}
			break;
			case "bool": case "boolean": case "check": 
				$theValue = ($theValue ==""||$theValue=="0"||$theValue =="false") ?$theNotDefinedValue:$theDefinedValue;
			break;
			default: 
				$theValue = (!empty($theValue)) ? $theValue : ""; 
			break; 
		}
		return $theValue;
	}
}
function DGXtract($strHeader,$strInic,$strFin){
	$pInicial = strpos($strHeader, $strInic);
	$strFinal = substr($strHeader,$pInicial+strlen($strInic),strlen($strHeader));
	$pFinal = strpos($strFinal, $strFin);
	$strFinal = substr($strFinal,0,$pFinal);
	return $strFinal;
}
function magic_quote($value){
   if (get_magic_quotes_gpc()) $value = stripslashes($value);
   if (!is_numeric($value)) $value = "'" . mysql_real_escape_string($value) . "'";
   return $value;
}

?>
