<?php

namespace vy;

class Vari
   implements Expr
{

   /// argumentum fajta olvasása, vagy null
   static function readKind( Stream $s ) {
      $s->readWS();
      $ret = $s->next();
      if ( in_array( $ret, [self::DEF, self::VAL, self::REF ] )) {
         $s->read();
         return $ret;
      }
      return null;
   }

   const
      DEF = ":",
      REF = "$",
      VAL = "&";

   protected $name;

   function __construct( $name ) {
	   $this->name = $name;
	}

   function run( RunCtx $ctx ) {
      return $ctx->getVar( $this->name );
   }

   function name() { return $this->name; }
	
   function __toString() {
	   return "<$".$this->name.">";
	}
   
   protected function updateKind( Stream $s ) {
      if ( ! $k = self::readKind( $s )) return;
      if ( self::DEF != $k ) {
         if ( $this->kind && $this->kind != $k )
            throw new EVy("Argument kind mismatch: $this->kind $k");
         $this->kind = $k;
      }
      return true;
   }
   
}
