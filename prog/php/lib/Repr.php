<?php

namespace vy;

/// egy reprezentációs elem
class Repr {
	
	const
	   PUBLIC = "public";

    const
       INHERIT = "inherit",
       NATIVE = "native",
       REFCOUNT = "refcount",
       MANAGED = "managed";
	
	protected $name;
	protected $public;
	protected $kind;
	protected $old;
	protected $fields;
	
	function name() { return $this->name; }
	
    function read( Stream $s ) {
	   $s->readWS();
	   $this->name = $s->readIdent();
	   $s->readWS();
	   $s->read(":");
	   $s->readWS();
	   if ( $s->readIf( self::PUBLIC ))
	      $this->public = true;
	   $this->readKind( $s );
	   if ( ! $this->readFields( $s ) )
          $s->readToken(";");
	}
	
	/// fajta olvasása
	protected function readKind( $s ) {
	   $s->readWS();
	   $k = $this->kind = $s->readIdent();
	   switch ( $k ) {
		  case self::INHERIT: case self::NATIVE:
		     $s->readWS();
		     $this->old = $s->readIdent();
		  break;
		  case self::REFCOUNT: case self::MANAGED; break;
		  default:
		     throw new EVy("Unknown representation kind: $k");
	   }
	}
	
	/// plusz mezők olvasása
	protected function readFields( $s ) {
	   $s->readWS();
	   if ( self::NATIVE == $this->kind ) return false;
	   if ( ! $s->readIf("{") ) return false;
	   $this->fields = [];
	   while ( $this->readField( $s ))
	      ;
	   $s->readToken("}");
	   return true;
	}
	
	/// egy plusz mező olvasása
	protected function readField( $s ) {
	   $s->readWS();
	   if ( "}" == $s->next() )
	      return false;
	   $name = $s->readIdent();
	   if ( array_key_exists( $name, $this->fields ))
	      throw new EVy("Duplicate field: $name");
	   $s->readWS();
	   $s->readToken(":");
	   $s->readWS();
	   $typ = $s->readIdent();
	   $s->readWS();
	   $s->readToken(";");
	   $this->fields[$name] = $typ;
	   return true;
	}
	   
		     
		     
		  
	   
		   
	   	
	
}
