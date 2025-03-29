<?php

namespace vy;

/// field and value
class FieldVal
   implements Expr
{

   public $field;
   public $val;
	
   function __construct( $field, $val ) {
	  $this->field = $field;
     $this->val = $val;
   }
	
	function __toString() {
	   return "<".$this->field.":".$this->val.">";
	}
	
   function run( RunCtx $ctx ) {
      return $this->val;
   }
}
