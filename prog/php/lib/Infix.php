<?php

namespace vy;

/// infix kifejezés
class Infix
   implements Expr
{

   protected $op;
   protected $left;
   protected $right;

   function __construct( $op, Expr $left, Expr $right ) {
      $this->op = $op;
      $this->left = $left;
      $this->right = $right;
   }

   function __toString() {
      return sprintf("<%s%s%s>", $this->left, $this->op, $this->right );
   }

}
