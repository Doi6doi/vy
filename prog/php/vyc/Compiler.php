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
   /// író
   protected $writer;
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
      for ( $i =0; $i<count($this->inputs); ++$i ) {
         if ( ! Tools::g( $this->objs, $i ))
            $this->forceInput($i);
      }
   }

   /// egy kimenet kiírása
   function write( $obj, $out ) {
      $obj->readPhase(true);
      $this->setWriter( $this->extWriter( Tools::extension($out) ) );
      $this->writer->writeFile($obj, $out);
   }
   
   /// szükséges kimenetek kiírása
   function writeAll() {
      for ( $i=0; $i<count($this->outputs); ++$i) {
         $ii = Tools::g( $this->inputs, $i );
         $bi = Tools::g( $this->objs, $i );
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

   /// író beállítása
   function setWriter( CompWriter $w ) {
      if ( $this->writer && get_class($this->writer) == get_class($w) )
         return;
      $this->writer = $w;
      $w->setReprs( $this->reprs );
      $w->setTypeMap( $this->typemap );
   }

   /// író kiterjesztés alapján
   protected function extWriter( $ext ) {
      switch ($ext) {
         case ".h": case ".c": return new CWriter();
         case ".php": return new PhpWriter();
         default: throw new EVy("Unknown output extension: $ext");
      }
   }

   /// fordítás utáni takarítás
   protected function done() {
	  $this->inputs = [];
	  $this->outputs = [];
	  $this->objs = [];
   }

   /// egy bemenet olvasása
   protected function forceInput( $i ) {
      $inp = $this->inputs[$i];
      if ( preg_match('#^(.+)(@.+)$#',$inp,$m)) {
         $inp = $m[1];
         $v = Version::parse( $m[2], true );
      } else {
         $v = Version::untilNow();
      }
      $o = $this->repo->force( $inp, $v );
      $o->readPhase(true);
      $this->objs[ $i ] = $o;
   }

}
