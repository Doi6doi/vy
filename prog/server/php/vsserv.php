<?php

require_once("autoload.php");

/// vs szerver
class VSServ {

   /// singleton példány
   static $ins;
   /// interfészek
   protected $intfs;
   /// műveletek
   protected $meths;
   /// írás-olvasás
   protected $stream;

   function __construct() {
      $this->intfs = [];
      $rf = VIReflect::$ins;
      $this->addIntf( [VIServ::$ins, $rf] );
      $this->meths = [];
      $this->addFunc( [VRObjects::$ins, VRFunctions::$ins] );
      $this->stream = new VSStream();
   }

   /// szerver futtatása
   function run() {
      $this->helo();
      while ( $cmd = $this->stream->readCommand() )
         $this->process( $cmd );
   }

   /// üdvözlő adatok
   protected function helo() {
      $this->stream->writeCommand( new VCHelo( [
         VIServ::$ins, VIReflect::$ins
      ]));
   }

   /// interfész felvétele
   protected function addIntf( $intf ) {
      if ( is_array( $intf )) {
         foreach ( $intf as $i )
            $this->addIntf( $i );
      } else {
         $this->intfs[ "".$intf->handle() ] = $intf;
      }
   }

   /// függvény felvétele
   protected function addFunc( $func ) {
      if ( is_array( $func )) {
         foreach ( $func as $f )
            $this->addFunc( $f );
      } else {
         $this->intfs[ "".$func->handle() ] = $func;
      }
   }
}

VSServ::$ins = new VSServ();
VSServ::$ins->run();
