<?php

namespace vy;

class Autoload {

   protected static $path = [__DIR__];

   /// hozzáadás az elérési úthoz
   static function addPath( $dir ) {
      if ( is_array( $dir )) {
         foreach ( $dir as $d )
            self::addPath($d);
      } else {
		 if ( ! in_array( $dir, self::$path ))
            array_push( self::$path, $dir );
      }
   }

   static function autoload( $cls ) {
      if ( preg_match('#^vy\\\\(.*)$#', $cls, $m ))
         $cls = $m[1];
      foreach ( self::$path as $dir ) {
         $fname = $dir."/$cls.php";
         if ( file_exists( $fname )) {
           require_once( $fname );
           return;
         }
      }
      require_once("$cls.php");
   }

}

spl_autoload_register( ["vy\Autoload","autoload"]);
