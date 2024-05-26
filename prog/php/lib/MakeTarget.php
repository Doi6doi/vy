<?php

namespace vy;

/// egy make cél
class MakeTarget 
   extends Block
{
	
   /// cél neve
   protected $name;	
	
   function __construct( Make $owner ) {
	  parent::__construct( $owner, Block::BODY );
   }	
	
   function name() { return $this->name; }	
	
   /// cél olvasása	
   function read( ExprStream $s ) {
	  $s->readWS();
	  $this->name = $s->readIdent();
	  parent::read( $s );
   }	

}
