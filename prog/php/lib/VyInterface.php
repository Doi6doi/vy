<?php

/// interfész leíró fájl
class VyInterface {

   const
      INTERFACE = "interface";

   /// csomag
   protected $pkg;
   /// név
   protected $name;
   /// verzió
   protected $ver;

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
      $this->name = $s->readIdent();
      while ( $s->readIf(".")) {
         $this->pkg = ($this->pkg ? $this->pkg."." : "" ).$this->name;
         $this->name = $s->readIdent();
      }
      $s->readWS();
      $this->ver = $s->readVer();
      $s->readWS();
      $s->readToken("{");
   }

   /// egy rész olvasása
   protected function readPart( $s ) {
      $s->readWS();
      switch ( $n = $s->next() ) {
         default: throw new Exception("Unknown part: $n");
      }
   }

}
