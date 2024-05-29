<?php

namespace vy;

class MakeC extends MakeImport {

   const
      C = "C";

   function __construct( $owner ) {
	  parent::__construct( $owner, self::C );
	  $this->addFuncs( ["libFile","objFiles"] );
   }

   /// könyvtár neve
   function libFile( $name ) {
	  switch ( $sys = Tools::system() ) {
		 case Tools::WINDOWS: return $name.".dll";
		 case Tools::LINUX: return "lib".$name.".so";
		 default: throw new EVy("Unknown system: $sys");
      }
   }
 

}
