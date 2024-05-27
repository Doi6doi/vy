<?php

namespace vy;

// zárójeles kifejezés
class Braced
   implements Expr
{

   const
      CURLY = "{}",
      ROUND = "()",
      SQUARE = "[]";

   static function opener($kind) {
      return $kind[0];
   }

   static function closer($kind) {
      return $kind[1];
   }

   static function pair($tok) {
      switch ( $tok ) {
         case "(": return ")";
         case ")": return "(";
         case "[": return "]";
         case "]": return "[";
         case "{": return "}";
         case "}": return "{";
         default: return null;
      }
   }

   protected $kind;
   protected $body;

   function __construct( $kind, $body=null ) {
      $this->kind = $kind;
      $this->body = $body;
   }

   function kind() { return $this->kind; }

   function body() { return $this->body; }

   function run( RunCtx $ctx ) {
	  switch ($this->kind) {
		 case self::ROUND: 
		 case self::SQUARE: 
		    return $this->body->run( $ctx );
		 default: throw new EVy("Cannot run braced: ".$this->kind);
	  }
   }

   function __toString() {
      return sprintf("<%s%s%s>", self::opener($this->kind),
         $this->body, self::closer($this->kind) );
   }

}
