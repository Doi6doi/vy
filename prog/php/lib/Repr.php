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
	
	function kind() { return $this->kind; }
	
	function old() { return $this->old; }
	
	function str() {
       switch ( $this->kind ) {
		  case self::INHERIT: 
		  case self::REFCOUNT:
		  case self::MANAGED:
		     return $this->name;
		  case self::NATIVE: return $this->old;
		  default: return $this->unKind();
	   }
	}

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
		     throw $this->unKind();
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

   protected function unKind() {
      return new EVy("Unknown representation kind:".$this->kind);
   }	   
	
}
