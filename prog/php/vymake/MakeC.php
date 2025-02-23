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

   function literal( $s ) {
      return CCompiler::literal( $s, 72 );
   }

}
