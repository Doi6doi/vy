<?php

namespace vy;

/// vy fordító
class Compiler {

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
      $this->repo = new RepoMulti();
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
      $this->forceInputs();
      $this->transform();
      $this->writeAll();
   }

   /// minden bemenet beolvasása
   function forceInputs() {
      foreach ($this->inputs as $i) {
         if ( ! array_key_exists( $i, $this->objs ))
            $this->forceInput( $i );
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
   function forceInput( $i ) {
      if ( preg_match('#^(.+)(@.+)$#',$i,$m))
         $o = $this->repo->force( $m[1], $m[2] );
         else $o = $this->repo->force( $i, null );
      $this->objs[ $i ] = $o;
   }

}
