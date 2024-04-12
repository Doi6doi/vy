<?php

namespace vy;

/// függvény argumentum, vagy változó
class Arg
   implements Expr
{

   protected $owner;
   protected $name;
   protected $type;

   function __construct( ExprCtx $owner, $name=null, $type=null ) {
      $this->owner = $owner;
      $this->name = $name;
      $this->type = $type;
   }

   function read( Stream $s ) {
      $s->readWS();
      $this->name = $s->readIdent();
      $s->readWS();
      if ( $s->readIf(":") ) {
         $this->type = $this->owner->readType( $s );
      } else {
         $this->type = $this->name;
         $this->owner->checkType( $this->type );
         $this->name = null;
      }
   }

   function __toString() {
      return "<".$this->name.">";
   }

}
