<?php

namespace vy;

class RunFrame {

   protected $name;
   protected $vars;

   function __construct( $name ) {
	  $this->name = $name;
	  $this->vars = [];
   }

}
