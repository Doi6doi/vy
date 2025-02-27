<?php

namespace vy;

/// eszköz importja
abstract class MakeImportCmd extends MakeImport {
   
   function __construct( $owner, $name ) {
	   parent::__construct( $owner, $name );
 	   $this->addFuncs( ["get","set"] );
   }

   /// a parancssori eszköz objektuma
   abstract function cmd();

   /// változó beállítása
   function set( $fld, $val ) {
      $this->cmd()->set( $fld, $val );
   }
   
   /// változó lekérdezése
   function get( $fld ) {
      return $this->cmd->get( $fld );
   }
   
}
