<?php

/// reflection interfész
class VIReflect extends VIntf {

   static $ins;

   function __construct() {
      $this->addFunc( VRObjects::$ins );
      $this->addFunc( VRFunctions::$ins );
   }

   function name() { return VSC::REFLECT; }

   function version() { return "20230218"; }

   function pkg() { return VSC::VS; }

}

VIReflect::$ins = new VIReflect();
