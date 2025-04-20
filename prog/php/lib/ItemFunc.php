<?php

namespace vy;

/// valamilyen függvény
abstract class ItemFunc
   extends ExprCtxForward
   implements Expr
{

   /// egyező osztály készítése
   static function create( Item $owner, ItemFunc $other ) {
      $dt = $owner->defType();
      if ( $other instanceof ItemConst )
         return new ItemConst( $owner, $dt ); 
      else if ( $other instanceof ItemMethod )
         return new ItemMethod( $owner, $dt );
      else if ( $other instanceof ItemFunction )
         return new ItemFunction( $owner, $dt );
      else
         throw new EVy("Unknown func: ".get_class($other));
   }

   /// név
   protected $name;
   /// argumentumok
   protected $sign;
   /// függvény törzse
   protected $body;

   function __construct( ExprCtx $owner ) {
	   parent::__construct( $owner );
      $this->sign = new Sign( $this, true );
      if ( $this->owner->isImplem() )
         $this->body = new Block( $this );
   }

   function name() { return $this->name; }

   function sign() { return $this->sign; }

   function defType() { return $this->owner->defType(); }

   function blockKind() {
      return $this->body ? Block::BODY : Block::NONE;
   }

   function run( RunCtx $ctx ) {
      return $this;
   }

   function __toString() { return "<".$this->name().">"; }

   function read( Stream $s ) {
      $this->name = $s->readIdent();
      $this->sign->read( $s );
      $s->readWS();
      $this->readDetails( $s );
   }

   /// jellemzők öröklése
   function inherit( $other, $map ) {
      $this->name = $other->name();
      $this->sign->inherit( $other->sign(), $map );
   }

   /// kompatibilitás ellenőrzése
   function checkCompatible( $other, $map ) {
      if ( get_class($this) != get_class($other)) 
         throw $this->notComp( $other, "class");
      $this->sign()->checkCompatible( $other->sign(), $map );
   }

   /// kompatibilitási hiba
   protected function notComp( $other, $reason ) {
      throw new EVy(sprintf( "function %s (%s->%s) not compatible: %s",
         $this->name(), $other->owner()->name(), $this->owner()->name(),
         $reason ));
   }

   /// további részletek olvasása
   protected function readDetails( $s ) {
      if ( $s->readIf(";") ) 
         return;      
      $s->readToken("{");
      while ( true ) {
         $s->readWS();
         if ( ! $this->readDetail( $s ))
            break;
      }
      $s->readToken("}");
   }

   /// egy részlet olvasása
   protected function readDetail( $s ) {
      switch ( $s->next() ) {
         case "}": return false;
         default:
            if ( $this->body )
               return $this->body->readPart( $s );
               else throw $s->notexp("detail");
      }
   }

}
