<?php

namespace vy;

/// git eszköz ős
class PToolGit extends ToolCmd {

   /// konfig mezők
   const
      DEPTH = "depth";
   
   function __construct($exe) {
	   parent::__construct($exe);
 	   $this->addFuncs( ["clone"] );
   }

   function clone( $url ) {
      throw Tools::notImpl($this,"clone");
   }
   
   function logFmt( $meth ) {
      switch ($meth) {
         case "clone": return "Cloning: %s";
         default: return parent::logFmt($meth);
      }
   }
   
   protected function confKind( $fld ) {
      switch ($fld) {
         case self::DEPTH: return Configable::SCALAR;
         default: return parent::confKind( $fld );
      }
   }
   


}
