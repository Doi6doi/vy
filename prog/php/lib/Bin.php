<?php

namespace vy;

/// vy bináris osztály
class Bin {

   protected $filename;
   protected $pool;
   protected $items;

   function load( $fname ) {
      $this->filename = $fname;
      if ( ! $fh = fopen( $fname, "r" ) )
         throw new EVy("Cannot open $fname");
      $s = new VyBStream( $fname );
      $this->loadStream( $s );
      fclose( $fh );
   }

   function run( Context $ctx ) {
      if ( ! $e = $this->info()->entry() )
         throw new EVy("Missing entry point");
      $this->method( $e )->execute();
      $bin->method( $e )->run( $ctx );
   }

   function clear() {
      $this->pool = new Pool();
      $this->items = [];
   }

   function loadStream( BStream $s ) {
      $this->clear();
      $this->loadMagic( $s );
      $this->pool->load( $s );
      $this->items = $s->readList();
   }

   function loadMagic( BStream $s ) {
      $s->readToken( "\x7d" );
      $t = $s->readString();
      if ( "vyb.Bin" != $t )
         throw new EVy("Unkown binary: $t");
      $v = $s->readVer();
      if ( "@20240118" < $v )
         throw new EVer("Too new version: $v");
   }

}
