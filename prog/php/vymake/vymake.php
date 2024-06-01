<?php

require_once( __DIR__."/../lib/autoload.php" );

vy\Autoload::addPath( __DIR__ );

ini_set('zend.exception_ignore_args', 0);

/// vy make eszköz
class VyMake {

   const
      MAKEVY = "Make.vy";

   protected $fullStack;
   protected $file;
   protected $targets;

   function __construct() {
      $this->targets = [];
   }

   function run( $argv ) {
      try {
         $this->getParams( $argv );
         $make = vy\Make::load( $this->file );
         $make->run( $this->targets );
      } catch ( Throwable $e ) {
         if ( $this->fullStack )
            print( vy\Tools::shortTrace( $e ));
         print( sprintf( "%s (%s:%s): %s\n",
            get_class($e), basename( $e->getFile() ),
            $e->getLine(), $e->getMessage() ));
      }         
   }

   /// paraméterek olvasása
   function getParams( $argv ) {
      try {
         $at = 1;
         while ( $this->getParam( $argv, $at ))
            ;
         if ( ! $this->file )
            $this->file = self::MAKEVY;
      } catch (Exception $e) {
         $this->usage($e->getMessage());
      }
   }

   /// egy paraméter olvasása
   function getParam( $argv, & $i ) {
      if ( $i >= count($argv))
         return false;
      switch ($a = $argv[$i]) {
         case "-x":
            $this->fullStack = true;
         break;
         default: 
            $this->targets [] = $a;
      }
      ++ $i;
      return true;
   }

   /// használat
   function usage( $msg ) {
      $ret = [
         "",
         "Usage: php vymake.php <options> [<target>..]",
         "",
         "Options:",
         "   -x: show full exception stack",
         "",
         ""
      ];
      fprintf( STDERR, implode("\n",$ret) );
      if ( $msg )
         fprintf( STDERR, "$msg\n" );
      exit(1);
   }


}

(new VyMake())->run( $argv );
