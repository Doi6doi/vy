<?php

/// vy fordító
class VyCompiler {

   /// szükséges tár
   protected $repo;
   /// bemenetek
   protected $inputs;
   /// beolvasott objektumok
   protected $objs;
   /// kimenetek
   protected $outputs;

   function __construct() {
      $this->inputs = [];
      $this->outputs = [];
      $this->objs = [];
      $this->repo = new VyRepoMulti();
   }

   function repo() { return $this->repo; }

   /// új bemenet
   function addInput( $i ) {
      $this->inputs [] = $i;
   }

   /// új kimenet
   function addOutput( $o ) {
      $this->outputs [] = $o;
   }

   /// futtatás
   function run() {
      $this->readAll();
      $this->transform();
      $this->writeAll();
   }

   /// minden bemenet beolvasása
   function readAll() {
      foreach ($this->inputs as $i) {
         if ( ! array_key_exists( $i, $this->objs ))
            $this->read( $i );
      }
   }

   /// átalakítás, ha szükséges
   function transform() {
   }

   /// szükséges kimenetek kiírása
   function writeAll() {
      foreach ( $this->outputs as $o )
         $this->write( $o );
   }

   /// egy bemenet olvasása
   function read( $i ) {
      $this->objs[ $i ] = $this->repo->read( $i );
   }

/*
   /// bemeneti fájl olvasása
   function read() {
      $s = $this->stream = new VyStream( $this->infile );
      $s->readWS();
      switch ( $k = $s->next() ) {
         case VyInterface::INTERFACE: $this->obj = new VyInterface(); break;
         default: throw new Exception("Unknown file: $k" );
      }
      $this->obj->read( $s );
   }
*/


}
