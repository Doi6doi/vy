<?php

namespace vy;

/// függvény argumentum, vagy változó
class Arg
   implements Expr, Vari
{
   const
      ARG = "arg";

   const
      VAR = "var",
      OUT = "out";

   protected $owner;
   protected $name;
   protected $type;
   protected $kind;

   function __construct( ExprCtx $owner, $name=null, $type=null, $kind=null ) {
      $this->owner = $owner;
      $this->name = $name;
      $this->type = $type;
      $this->kind = $kind;
   }

   function name() { return $this->name; }

   function type() { return $this->type; }

   function kind() { return $this->kind; }

   function read( Stream $s, $typed ) {
      $this->readKind( $s );
      $this->name = $s->readIdent();
      $s->readWS();
      if ( $typed ) {
         if ( $s->readIf(":") ) {
            $this->type = $this->owner->readType( $s );
         } else {
            $this->type = $this->name;
            $this->owner->checkType( $this->type );
            $this->name = null;
         }
      }
   }

   function run( RunCtx $ctx ) {
	  return $ctx->var( $this->name );
   } 

   function checkCompatible( Arg $other, $map ) {
      if ( $this->type() != Tools::gc( $map, $other->type() ))
         throw $this->notComp( $other, "type");
   }

   function __toString() {
      return "<".$this->name.">";
   }

   /// argumentum fajta olvasása
   protected function readKind( $s) {
      $s->readWS();
      if ( $s->readIf( "&" ))
         $this->kind = self::VAR;
      else if ( $s->readIf("^"))
         $this->kind = self::OUT;
      $s->readWS();
   }      

   protected function notComp( Arg $other, $reason ) {
      return new EVy("Not comaptible arg: ".$this->name().": ".$reason );
   }



}
