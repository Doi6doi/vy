<?php

namespace vy;

/// reprezentációk
class Reprs {
	
   const
      REPRESENTATION = "representation";
	
   protected $items;
   
   function __construct() {
	  $this->items = [];
   }
   
   /// fájl felolvasása
   function read( Stream $s ) {
	  $s->readWS();
	  $s->readToken( self::REPRESENTATION );
	  $s->readWS();
	  $s->readToken("{");
	  while ( $this->readItem( $s ))
	     ;
	  $s->readToken("}");
   }
	
   /// egy elem felolvasása
   protected function readItem( $s ) {
	  $s->readWS();
	  if ( "}" == $s->next() )
	     return false;
	  $r = new Repr();
	  $r->read( $s );
	  $this->add( $r );
	  return true;
   }
   
   /// reprezentáció hozzáadása
   protected function add( $r ) {
	  $n = $r->name();
	  if ( array_key_exists( $n, $this->items ))
	     throw new EVy("Duplicate item: $n" );
	  $this->items[ $n ] = $r;
   }
   
}
