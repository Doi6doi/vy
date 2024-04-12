<?php

namespace vy;

class Autoload {

   protected static $path = [".","../lib"];

   /// hozzáadás az elérési úthoz
   static function addPath( $dir ) {
      if ( is_array( $dir )) {
         foreach ( $dir as $d )
            self::addPath($d);
      } else {
         array_push( self::$path, $dir );
      }
   }

   static function autoload( $cls ) {
      if ( preg_match('#^vy\\\\(.*)$#', $cls, $m ))
         $cls = $m[1];
      foreach ( self::$path as $dir ) {
         $fname = $dir."/$cls.php";
         if ( file_exists( $fname ))
           require_once( $fname );
      }
      require_once("$cls.php");
   }

}

spl_autoload_register( ["vy\Autoload","autoload"]);
