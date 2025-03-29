<?php

namespace vy;

class ToolGdb extends ToolCmd {

   function __construct() {
      parent::__construct("gdb");
      $this->addFuncs(["debug"]);
   }

   function debug( $prg ) {
      $this->exec( this->esc($prg) );
   }
   
}
