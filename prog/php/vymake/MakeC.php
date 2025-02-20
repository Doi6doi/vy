<?php

namespace vy;

class MakeC extends MakeCompiler {

   const
      C = "C";

   function __construct( $owner ) {
      parent::__construct( $owner, self::C );
	   $this->addFuncs( ["setIncDir"] );
   }

   /// include könyvtár beállítása
   function setIncDir( $dir ) {
	  return $this->compiler->setIncDir( $dir );
   }

   /// fordító beállítása
   function setCompiler( $cmp ) {
	  $this->compiler = CCompiler::create( $cmp );
   }

}
