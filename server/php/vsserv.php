<?php

require_once("autoload.php");

/// vs szerver
class VSServ {

   /// interfészek
   protected $intfs;
   /// írás-olvasás
   protected $stream;

   function __construct() {
      $this->intfs = [];
      $this->addIntf( [VIServ::$ins, VIReflect::$ins] );
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
      $this->stream->writeCommand( new VSHelo( [
         VIServ::$ins, VIReflect::$ins
      ]));
   }

   /// interfész felvétele
   protected function addIntf( $intf ) {
      if ( is_array( $intf )) {
         foreach ( $intf as $i )
            $this->addIntf( $i );
      } else
         $this->intfs[ $intf->fullName() ] = $intf;
   }
}

(new VSServ())->run();
