<?php

/// interfész leíró fájl
class VyInterface {

   const
      EXTEND = "extend",
      INTERFACE = "interface";

   /// csomag
   protected $pkg;
   /// név
   protected $name;
   /// verzió
   protected $ver;
   /// ősinterfészek
   protected $extend;

   function __construct() {
      $this->extend = [];
   }

   /// fájl beolvasása
   function read( VyStream $s ) {
      $this->readHead( $s );
      while (true) {
         $s->readWS();
         if ( $s->readIf("}"))
            return;
         else
            $this->readPart( $s );
      }
   }

   /// fejrész beolvasása
   protected function readHead( $s ) {
      $s->readWS();
      $s->readToken( self::INTERFACE );
      $s->readWS();
      $this->pkg = $s->readIdents();
      $this->name = array_pop( $this->pkg );
      $s->readWS();
      $this->ver = $s->readVer();
      $s->readWS();
      $s->readToken("{");
   }

   /// egy rész olvasása
   protected function readPart( $s ) {
      $s->readWS();
      switch ( $n = $s->next() ) {
         case self::EXTEND: return $this->readExtend( $s );
         default: throw new Exception("Unknown part: $n");
      }
   }

   /// extend rész olvasása
   protected function readExtend( $s ) {
      $s->readToken( self::EXTEND );
      $s->readWS();
      if ( $s->readIf("{")) {
         while (true) {
            $this->readExtendItem( $s );
            $s->readWS();
            if ( ! $s->readIf(","))
               break;
         }
         $s->readToken("}");
      } else {
         $this->readExtendItem($s);
         $s->readWS();
         $s->readToken(";");
      }
   }

   /// extend elem olvasása
   protected function readExtendItem( $s ) {
      $pkg = $s->readIdents();
      $name = array_pop( $pkg );
      throw new Exception("nyf");
   }

}
