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
 	   $this->addFuncs( ["arch","build"] );
   }

   /// csomag építése
   function build( $dir, $fname=null ) {
      $this->deb->build( $dir, $fname );
   }
   
   /// architektúra elnevezés
   function arch( $x ) {
      return $this->deb->arch( $x );
   }
   
}
