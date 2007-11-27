<?php
/**
 * Filename:  includes/language/compare.php
 * Function:  Compares the language files
 * @author  Lloyd Borrett - www.borrett.id.au Rob Lensen <rob@bsdfreaks.nl>
 * @package phpdivinglog
 * @version $Rev$
 * Last Modified: $Date$
 */

// Place this script in the folder where the language files are.
// Call it from the browser: 
//     http://example.com/divelog/includes/language/compare.php?f=danish 
// to check "danish.inc.php"
//
// NOTE:
// Those of you looking to write a labguage file, might want to
// look at using phpLanguageEditor - http://phplangeditor.mozdev.org/
//

// check that the file exists
   $f = $_GET['f'];
   if ( empty( $f ) || strpos( $f, '/' ) > 0 || !file_exists( "$f.inc.php" ) ) die( "File $f.inc.php not found." );

// get the english language file
   require( "english.inc.php" );
   $eng = array_keys( $_lang );
   unset( $_lang );

// get the language file to check
   require( "$f.inc.php" );
   $other = array_keys( $_lang );

   echo "Missing from $f.inc.php:<br>\n<dl>\n";
   foreach ( $eng as $key )
   {
       if ( false === ( $dest = array_search( $key, $other ) ) )
       {
           echo "<dd>$key</dd>\n";
       }
       else 
       {
           unset( $other[ $dest ] );
       }
   }
   $other = array_unique( $other );
   echo "</dl>\n";
   echo "<hr>\n";

   echo "In $f.inc.php but not in english.inc.php:<br>\n<dl>\n";
   foreach ( $other as $key )
   {
       if ( !in_array( $key, $eng ) )
       {
           echo "<dd>$key</dd>\n";            
       }
   }
   echo "</dl>\n";
   echo "<hr>\n";
   echo "<br>\n### END ###<br>\n";
?>
