<?php

namespace vy;

class MakeC extends MakeImport {

   const
      C = "C";

   function __construct( $owner ) {
	  parent::__construct( $owner, self::C );
	  $this->addFunc( "objFiles" );
	  $this->addFunc( "libFile" );
   }

}
