<?php

/// functions reflect függvény
class VRFunctions extends VSFunc {

   static $ins;

   function name() { return VSC::FUNCTIONS; }

}

VRFunctions::$ins = new VRFunctions();


