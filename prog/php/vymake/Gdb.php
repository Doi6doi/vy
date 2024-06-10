<?php

namespace vy;

class Gdb extends Debugger {

   function executable() { return "gdb"; }
   
   function debug( $prg ) {
      $this->run( "%s", $prg );
   }
   
}
