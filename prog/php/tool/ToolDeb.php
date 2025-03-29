<?php

namespace vy;

/// debian csomagoló
class ToolDeb extends ToolCmd {

   function __construct() {
      parent::__construct("dpkg-deb");
      $this->set( self::SHOW, true );
 	   $this->addFuncs( ["arch","build"] );
   }

   /// architektúra konvertálás deb architektúrává
   function arch( $arch ) {
      switch ( $arch ) {
         case "x86_64": return "amd64";
         default:
            throw new EVy("Unknown architecture: $arch");
      }
   }

   /// csomag építése
   function build( $dir, $fname = null ) {
      if ( ! $fname )
         $fname = $this->getName( $dir );
      return $this->exec( "--build --root-owner-group", 
         $this->esc($dir), $this->esc($fname) ); 
   }

   /// név control fájl alapján
   protected function getName( $dir ) {
      $c = $this->loadControl( $dir );
      if ( ! $n = $this->getField( $c, "Package" ))
         throw new EVy("No Name in control");
      if ( ! $v = $this->getField( $c, "Version" ))
         throw new EVy("No Version in control");
      if ( ! $a = $this->getField( $c, "Architecture" ))
         throw new EVy("No Architecture in control");
      $r = $this->getField( $c, "Revision" );
      return sprintf( "%s_%s%s_%s.deb", $n, $v, $r ? "-$r" : "", $a );
   }
         
   /// control fájl betöltése
   protected function loadControl( $dir ) {
      return Tools::loadFile( Tools::path( $dir, "DEBIAN", "control" ));      
   }         

   /// mező kiolvasása a control-ból
   protected function getField( $c, $fld ) {
      if ( preg_match('#^\s*'.$fld.'\s*:\s*(.*?)\s*$#m',$c, $m ))
         return $m[1];
      return null;
   }

}
