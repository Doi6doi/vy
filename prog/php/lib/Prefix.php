<?php

namespace vy;

/// prefix kifejezés
class Prefix
   implements Expr
{

   protected $op;
   protected $body;

   function __construct( $op, Expr $body ) {
      $this->op = $op;
      $this->body = $body;
   }

   function __toString() {
      return sprintf("<%s%s>", $this->op, $this->body );
   }

}
