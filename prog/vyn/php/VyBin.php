<?php

/// vy bináris osztály
class VyBin {

   protected $filename;
   protected $pool;
   protected $items;

   function load( $fname ) {
      $this->filename = $fname;
      if ( ! $fh = fopen( $fname, "r" ) )
         throw new Exception("Cannot open $fname");
      $s = new VyStream( $fname );
      $this->loadStream( $s );
      fclose( $fh );
   }

   function run( VyContext $ctx ) {
      if ( ! $e = $this->info()->entry() )
         throw new EVy("Missing entry point");
      $this->method( $e )->execute();
      $bin->method( $e )->run( $ctx );
   }

   function clear() {
      $this->pool = new VyPool();
      $this->items = [];
   }

   function loadStream( VyStream $s ) {
      $this->clear();
      $this->loadMagic( $s );
      $this->pool->load( $s );
      $this->items = $s->readList();
   }

   function loadMagic( VyStream $s ) {
      $s->readToken( "\x7d" );
      $t = $s->readString();
      if ( "vyb.Bin" != $t )
         throw new EVy("Unkown binary: $t");
   }

}
