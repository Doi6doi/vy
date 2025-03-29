<?php

namespace vy;

/// C++ fordító ős
class PToolCpp extends PToolC {

   const
      PP = "pp";
   
   function __construct($pp) {
      parent::__construct();
      $this->set( self::PP, $pp );
   }
   
   protected function confKind( $fld ) {
      switch ($fld) {
         case self::PP: return Configable::BOOL;
         default: return parent::confKind( $fld );
      }
   }
   
}
