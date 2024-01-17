<?php

function vs_autoload( $cls ) {
   require_once( "$cls.php" );
}

spl_autoload_register( "vs_autoload" );
