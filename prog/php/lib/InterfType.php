<?php

namespace vy;

class InterfType {

   /// tulajdonos
   protected $owner;
   /// név
   protected $name;
   /// egyezők
   protected $same;

   function __construct( $owner ) {
      $this->owner = $owner;
      $this->same = [];
   }

   function name() { return $this->name; }

   function read( Stream $s ) {
      while ( $this->readItem( $s ) )
         ;
      if ( ! $this->name )
         throw $s->notexp("type");
      $s->readToken(";");
  }

   protected function readItem( $s ) {
      $s->readWS();
      if ( ! $this->name ) {
         $arr = $s->readPath();
         $n = count($arr);
         $this->name = $arr[ $n-1 ];
         if ( 1 < $n )
            $this->same [] = implode(".",$arr);
         return true;
      } else if ( $s->readIf("=")) {
         $s->readWS();
         $arr = $s->readPath();
         $this->same [] = implode(".",$arr);
         return true;
      } else
         return false;
   }

}
