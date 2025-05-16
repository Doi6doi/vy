<?php

namespace vy;

/// stream-beli átugráshoz és visszatéréshez szükséges rész
class Skip {

   static function mark( Stream $s ) {
      $ret = new Skip();
      $ret->stream = $s;
      $ret->at = $s->at();
      return $ret;
   }

   static function jump( $s ) {
      if ( ! $s ) return;
      $ret = $s->stream;
      $ret->jump( $s->at );
      return $ret;
   }

}
