<?php

require_once( "../../lib/autoload.php");
VyAutoload::addPath(["..","../../lib"]);

class TestVyc extends VyTest {

   const
      ROOT = "../../..",
      VYC = "vyc.php",
      STRING = "vy.char.String",
      STRINGH = "vy_string.h";

   function run() {
      $this->chdir( ".." );
      $this->execPhp( self::VYC, sprintf( "-r %s -i %s -o %s",
         $this->escape(self::ROOT), self::STRING, self::STRINGH
      ));
   }
}

(new TestVyc())->run();
