<?php

/// hasznos cuccok
class Tools {

   /// asszociatív tömb
   static function isAssoc( array $x ) {
      $ret = array_key_first( $x ) === 0
         && array_key_last( $x ) === count($x)-1;
      return ! $ret;
   }

}
