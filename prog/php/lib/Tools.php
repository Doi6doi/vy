<?php

namespace vy;

class Tools {

   static function g($arr,$fld) {
      if ( is_array($arr) && array_key_exists($fld,$arr))
         return $arr[$fld];
      return null;
   }

   static function debug() {
      $ret = [];
      foreach (func_get_args() as $x) {
//         if ( ! is_string( $x ))
//            $x = json_encode( $x );
         $ret [] = $x;
      }
      fprintf( STDERR, implode(", ",$ret)."\n" );
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
         throw new EVy( "JSON error: ".json_last_error_msg() );
   }

   /// csomagnév útvonallá
   static function pkgDir( $x ) {
      return str_replace( ".","/",$x);
   }

   /// útvonal csomaggá
   static function dirPkg( $x ) {
      return str_replace( "/",".",$x);
   }

   /// változat feltétel teljesül-e
   static function verCond( $cond, $ver ) {
      if ( ! preg_match('#^@[0-9]{8}$#', $ver ))
         throw new EVy("Unknown version: $ver");
      if ( ! $cond )
         return true;
      if ( ! preg_match('#^@([<=>]*)([0-9]{8})$#', $cond, $m ))
         throw new EVy("Unknown condition: $cond");
      $cv = "@".$m[2];
      switch ( $cr = $m[1] ) {
         case "=": return $ver == $cv;
         case "<=": return $ver <= $cv;
         case ">=": return $ver >= $cv;
         default: throw new EVy("Unknown condition rel: $cr");
      }
   }

}
