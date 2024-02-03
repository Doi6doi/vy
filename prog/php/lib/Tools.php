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
      $ret = json_decode( $data, true );
      self::checkJson();
      return $ret;
   }

   static function checkJson() {
      if ( JSON_ERROR_NONE != json_last_error() )
         throw new Exception( "JSON error: ".json_last_error_msg() );
   }

}
