<?php

namespace vy;

/// vy fordító
class Compiler {

   /// szükséges tár
   protected $repo;
   /// reprezentációk
   protected $reprs;
   /// bemenetek
   protected $inputs;
   /// beolvasott objektumok
   protected $objs;
   /// kimenetek
   protected $outputs;
   /// típus megfeleltetés
   protected $typemap;
   /// c író
   protected $cWriter;
   /// létező fájl felüírása
   protected $force;
   
   function __construct() {
      $this->inputs = [];
      $this->outputs = [];
      $this->objs = [];
      $this->typemap = [];
      $this->repo = new RepoMulti();
      $this->reprs = [];
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

   /// kényszerítés beállítása
   function setForce( $force ) {
	  $this->force = $force;
   }

   /// reprezentációk beállítása
   function setReprs( $fns ) {
      $this->reprs = [];
      if ( ! $fns ) return;
      if ( ! is_array( $fns ))
         $fns = [$fns];
      foreach ( $fns as $fn )
         $this->addReprs( $fn );
   }

   /// reprezentációk hozzáadása
   function addReprs( $filename ) {
      $s = new Stream( $filename );
	   try {
         $r = new Reprs();
         $r->read( $s );
         $this->reprs [] = $r;
	  } catch (\Exception $e) {
		 throw new EVy( $s->position().": ".$e->getMessage(),
		    $e->getCode(), $e );
      }
   }

   /// futtatás
   function run() {
      $this->forceInputs();
      $this->writeAll();
      $this->done();
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
         if ( ! $this->force && file_exists($oi))
            throw new EVy("File already exists: $oi");
         $this->write( $bi, $oi );
      }
   }

   /// típusmegfeleltetés beállítása
   function setTypeMap( $maps ) {
      $this->typemap = [];
      if ( ! is_array( $maps ))
         $maps = explode(";",$maps);
      foreach ( $maps as $k=>$v ) {
         if ( preg_match('#^(.*)=(.*)$#', $v, $m )) {
            $k = $m[1];
            $v = $m[2];
         }
         $this->typemap[ $k ] = $v;
      } 
   }

   /// egy bemenet olvasása
   function forceInput( $i ) {
      if ( preg_match('#^(.+)(@.+)$#',$i,$m)) {
         $i = $m[1];
         $v = Version::parse( $m[2], true );
      } else {
         $v = Version::untilNow();
      }
      $o = $this->repo->force( $i, $v );
      $this->objs[ $i.$o->ver()->num() ] = $o;
   }

   /// c író
   function cWriter() {
      if ( ! $this->cWriter )
         $this->cWriter = new CWriter();
      $this->cWriter->setReprs( $this->reprs );
      $this->cWriter->setTypeMap( $this->typemap );
      return $this->cWriter;
   }

   /// fordítás utáni takarítás
   function done() {
	  $this->inputs = [];
	  $this->outputs = [];
	  $this->objs = [];
   }

}
