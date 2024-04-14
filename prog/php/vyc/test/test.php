<?php

require_once( "../../lib/autoload.php");

class TestVyc extends vy\Test {

   const
      ROOT = "../../..",
      VYC = "vyc.php",
      MAP = "Bool=&bool;Char=&wchar_t;Index=&unsigned",
      STRING = "vy.char.String",
      STRINGH = "test/vy_string.h";

   function run() {
      $this->chdir( ".." );
      $this->execPhp( self::VYC, sprintf( "-r %s -i %s -t %s -o %s",
         $this->escape(self::ROOT), self::STRING, $this->escape(self::MAP),
         self::STRINGH
      ));
   }
}

(new TestVyc())->run();
