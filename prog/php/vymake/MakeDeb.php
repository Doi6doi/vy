<?php

namespace vy;

/// debian .deb csomagoló
class MakeDeb extends MakeImport {
   
   const
      DEB = "Deb";

   protected $deb;
   
   function __construct( $owner ) {
	   parent::__construct( $owner, self::DEB );
      $this->deb = new Deb();
 	   $this->addFuncs( ["build"] );
   }

   /// csomag építése
   function build( $dir ) {
      $this->deb->build( $dir );
   }
   
}
