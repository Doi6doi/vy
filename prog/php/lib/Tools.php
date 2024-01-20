<?php

class Tools {

   static function g($arr,$fld) {
      if ( is_array($arr) && array_key_exists($fld,$arr))
         return $arr[$fld];
      return null;
   }

   static function allErrors() {
      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL);
   }

   static function loadFile( $fname ) {
      if ( false === ($ret = file_get_contents( $fname )))
         throw new EVy("Could not load file: $fname");
      return $ret;
   }

   static function jsonDecode( $data ) {
      return json_decode( $data, true, 512, JSON_THROW_ON_ERROR );
   }

}
