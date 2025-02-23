<?php

namespace vy;

/// Cpp fordító modul
class MakeCpp extends MakeCompiler {

   const
      CPP = "Cpp";

   function __construct( $owner ) {
      parent::__construct( $owner, self::CPP );
   }

   /// változó beállítása
   function setCompiler( $val ) {
      $this->compiler = CppCompiler::create( $val );
   }

}
