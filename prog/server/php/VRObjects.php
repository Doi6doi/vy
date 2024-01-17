<?php

/// objects reflect függvény
class VRObjects extends VSFunc {

   static $ins;

   function name() { return VSC::OBJECTS; }

}

VRObjects::$ins = new VRObjects();


