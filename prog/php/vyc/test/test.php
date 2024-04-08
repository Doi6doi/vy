<?php

require_once( "../../lib/autoload.php");
VyAutoload::addPath(["..","../../lib"]);

class TestVyc extends VyTest {

   const
      ROOT = "../../..",
      VYC = "vyc.php",
      STRINGV = self::ROOT."/vy/char/String@20240408.vy",
      STRINGH = "vy_string.h";

   function run() {
      $this->chdir( ".." );
      $this->buildFile( self::STRINGH, self::STRINGV, self::PHP,
         self::VYC." -i %source% -o %dest%" );
   }
}

(new TestVyc())->run();
