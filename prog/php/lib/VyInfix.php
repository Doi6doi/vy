<?php

/// infix kifejezés
class VyInfix
   implements VyExpr
{

   protected $op;
   protected $left;
   protected $right;

   function __construct( $op, VyExpr $left, VyExpr $right ) {
      $this->op = $op;
      $this->left = $left;
      $this->right = $right;
   }

   function __toString() {
      return sprintf("<%s%s%s>", $this->left, $this->op, $this->right );
   }

}
