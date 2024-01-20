<?php

function vyAutoload( $cls ) {
   foreach ( [".","../lib"] as $dir ) {
      $fname = $dir."/$cls.php";
      if ( file_exists( $fname ))
        require_once( $fname );
   }
   require_once("$cls.php");
}


spl_autoload_register( "vyAutoload");
