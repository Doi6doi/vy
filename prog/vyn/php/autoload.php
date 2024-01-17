<?php

function vyn_autoload( $cls ) {
   require_once("$cls.php");
}


spl_autoload_register( "vyn_autoload");
