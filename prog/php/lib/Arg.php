<?php

namespace vy;

/// függvény argumentum, vagy változó
class Arg
   implements Expr, Vari
{

   const
      ARG = "arg";

   const
      DEF = ":",
      REF = "$",
      VAL = "&";

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

   protected $owner;
   protected $name;
   protected $type;
   protected $kind;

   function __construct( ExprCtx $owner, $name=null, $type=null, $kind=null ) {
      $this->owner = $owner;
      $this->name = $name;
      $this->type = $type;
      if ( self::DEF == $kind )
         $kind = null;
      $this->kind = $kind;
   }

   function name() { return $this->name; }

   function type() { return $this->type; }

   function kind() { return $this->kind; }

   function defType() { return $this->owner->defType(); }

   function read( Stream $s, $typed ) {
      $uk = $this->updateKind($s);
      $s->readWS();
      if ( Stream::IDENT == $s->nextKind() ) {
         $this->name = $s->readIdent();
         if ( $typed ) {
            if ( $this->updateKind($s) ) {
               $this->type = $this->owner->readType( $s );
            } else {
               $this->type = $this->name;
               $this->owner->checkType( $this->type );
               $this->name = null;
            }
         }
      } else if ( $uk && $typed && $t = $this->defType() ) {
         $this->type = $t;
      } else
         throw $s->notexp("argument");
   }

   protected function updateKind( $s ) {
      if ( ! $k = self::readKind( $s )) return;
      if ( self::DEF != $k ) {
         if ( $this->kind && $this->kind != $k )
            throw new EVy("Argument kind mismatch: $this->kind $k");
         $this->kind = $k;
      }
      return true;
   }

   function run( RunCtx $ctx ) {
	  return $ctx->var( $this->name );
   } 

   function checkCompatible( Arg $other, $map ) {
      if ( $this->type() != Tools::gc( $map, $other->type() ))
         throw $this->notComp( $other, "type");
      if ( $this->kind() != $other->kind() )
         throw $this->notComp( $other, "kind");
   }

   function __toString() {
      return $this->name
         .($this->kind?$this->kind:":")
         .$this->type;
   }

   protected function notComp( Arg $other, $reason ) {
      return new EVy("Not comaptible arg: ".$this->name().": ".$reason );
   }



}
