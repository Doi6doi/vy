<?php

namespace vy;

class Literal
   implements Expr
{

   protected $value;

   function __construct( $value ) {
      $this->value = $value;
   }

   function __toString() {
      return "<".$this->value.">";
   }

}
