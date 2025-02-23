<?php

namespace vy;

class MakeC extends MakeCompiler {

   const
      C = "C";

   function __construct( $owner ) {
      parent::__construct( $owner, self::C );
   }

   function setCompiler( $val ) {
      $this->compiler = CCompiler::create( $val );
   }

}
