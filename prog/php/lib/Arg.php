<?php

namespace vy;

/// függvény argumentum
class Arg {

   protected $owner;
   protected $name;
   protected $type;

   function __construct( ExprCtx $owner ) {
      $this->owner = $owner;
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

}
