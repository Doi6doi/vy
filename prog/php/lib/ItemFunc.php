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
   
   /// olvasás átugorva innnentől
   protected $skip;
   /// olvasási fázis
   protected $phase;

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

   function read( ExprStream $s ) {
      $this->name = $s->readIdent();
      $this->skip = Skip::mark( $s );
      $this->phase = 1;
      $this->sign->skip($s);
      if ( ! $s->readTerm() )
         $s->skipBraces();
   }

   function readPhase( $phase ) {
      if ( true === $phase )
         $phase = 3;
      while ( $this->phase < $phase ) {
         $s = Skip::jump( $this->skip );
         ++ $this->phase;
Tools::debug("readPhase",$this,$this->phase);
         switch ($this->phase) {
            case 2:
               $this->sign->read( $s );
               $this->skip = Skip::mark($s);
            break;
            case 3:
               $this->readDetails($s);
               $this->skip = null;
            break;
         }
      }
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
      $s->readWS();
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
