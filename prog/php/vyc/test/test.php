<?php

require_once( "../../lib/autoload.php");

class TestVyc extends vy\Test {

   const
      ROOT = "../../..",
      VYC = "vyc.php",
      STRING = "vy.char.String",
      STRINGH = "test/vy_string.h";

   function run() {
      $this->chdir( ".." );
      $this->execPhp( self::VYC, sprintf( "-r %s -i %s -o %s",
         $this->escape(self::ROOT), self::STRING, self::STRINGH
      ));
   }
}

(new TestVyc())->run();
