<?php

/// vs interfész
abstract class VIntf extends VSHandled {

   /// interfész neve
   abstract function name();

   /// függvények
   protected $funcs;

   function __construct() {
      $this->funcs = [];
   }

   /// teljes név (csomag, név, verzió)
   function fullName() {
      $ret = $this->name();
      if ( $p = $this->pkg() )
         $ret = "$p:$ret";
      if ( $v = $this->version() )
         $ret = "$ret:$v";
      return $ret;
   }

   /// függvény név alapján
   function func( $name, $check=true ) {
      $ret = Tools::g( $this->funcs, $name );
      if ( ! $ret && $check )
         throw new EVS("Unknown function: $name in ".$this->name() );
   }

   /// függvény felvétele
   function addFunc( $func ) {
      $this->funcs[ $func->name() ] = $func;
   }

   function handleKind() { return VSC::CLIENTINTF; }
}
