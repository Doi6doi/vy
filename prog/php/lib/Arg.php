<?php

namespace vy;

/// függvény argumentum
class Arg
   extends Vari
{

   const
      ARG = "arg";

   protected $owner;
   protected $type;
   protected $kind;

   function __construct( ExprCtx $owner, $name=null, $type=null, $kind=null ) {
      parent::__construct( $name );
      $this->owner = $owner;
      $this->type = $type;
      if ( self::DEF == $kind )
         $kind = null;
      $this->kind = $kind;
   }

   function type() { return $this->type; }

   function kind() { return $this->kind; }

   function defType() { return $this->owner->defType(); }

   function ownerName() { return $this->owner->ownerName(); }  

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
      return new EVy(sprintf("Not compatible arg: %s.%s: %s",
         $this->ownerName(), $this->name(), $reason ));
   }

}
