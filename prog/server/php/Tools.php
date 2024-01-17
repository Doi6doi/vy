<?php

/// hasznos cuccok
class Tools {

   /// warningn nélküli get
   static function g( $arr, $fld ) {
      if ( is_array( $arr ) && array_key_exists( $fld, $arr ))
         return $arr[$fld];
      return null;
   }

   /// asszociatív tömb
   static function isAssoc( array $x ) {
      $ret = array_key_first( $x ) === 0
         && array_key_last( $x ) === count($x)-1;
      return ! $ret;
   }

}
