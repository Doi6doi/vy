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
   /// c író
   protected $cWriter;

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
      $this->writeAll();
   }

   /// minden bemenet beolvasása
   function forceInputs() {
      foreach ($this->inputs as $i) {
         if ( ! array_key_exists( $i, $this->objs ))
            $this->forceInput( $i );
      }
   }

   /// egy kimenet kiírása
   function write( $obj, $out ) {
      switch ($ext = Tools::extension($out)) {
         case ".h": $this->cWriter()->writeHeader($obj,$out); break;
         case ".c": $this->cWriter()->writeBody($obj,$out); break;
         case ".vy": $this->vyWriter()->write( $obj, $out ); break;
         default: throw new EVy("Unknown output extension: $ext");
      }
   }

   /// szükséges kimenetek kiírása
   function writeAll() {
      for ( $i=0; $i<count($this->outputs); ++$i) {
         $ii = Tools::g( $this->inputs, $i );
         $bi = Tools::g( $this->objs, $ii );
         $oi = $this->outputs[$i];
         $this->write( $bi, $oi );
      }
   }

   /// egy bemenet olvasása
   function forceInput( $i ) {
      if ( preg_match('#^(.+)(@.+)$#',$i,$m))
         $o = $this->repo->force( $m[1], $m[2] );
         else $o = $this->repo->force( $i, null );
      $this->objs[ $i ] = $o;
   }

   /// c író
   function cWriter() {
      if ( ! $this->cWriter )
         $this->cWriter = new CWriter();
      return $this->cWriter;
   }

}
