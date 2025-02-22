<?php

namespace vy;

/// Cpp fordító modul
class MakeCpp extends MakeCompiler {

   const
      CPP = "Cpp";

   function __construct( $owner ) {
      parent::__construct( $owner, self::CPP );
	   $this->addFuncs( ["setIncDir"] );
   }

   /// include könyvtár beállítása
   function setIncDir( $dir ) {
	  return $this->compiler->setIncDir( $dir );
   }

   /// fordító beállítása
   function setCompiler( $cmp ) {
	  $this->compiler = CppCompiler::create( $cmp );
   }

}
