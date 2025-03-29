<?php

namespace vy;

/// php függvény
class PhpFunc {

   protected $clb;

   function __construct( $clb ) {
      $this->clb = $clb;
   }
   
   function call( RunCtx $ctx, $args ) {
      return call_user_func_array( $this->clb, $args );
   }

}
