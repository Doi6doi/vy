<?php

require_once( __DIR__."/../lib/autoload.php" );

vy\Autoload::addPath( [__DIR__, __DIR__."/../tool"] );

ini_set('zend.exception_ignore_args', 0);

/// vy make eszköz
class VyMake {

   protected $fullStack;
   protected $help;
   protected $file;
   protected $targets;
   protected $urls;

   function __construct() {
      $this->targets = [];
      $this->urls = [];
   }

   function runCli( $argv ) {
      try {
         $this->run( $argv );
      } catch ( Throwable $e ) {
         if ( $this->fullStack )
            print( vy\Tools::shortTrace( $e ));
         print( sprintf( "%s (%s:%s): %s\n",
            get_class($e), basename( $e->getFile() ),
            $e->getLine(), $e->getMessage() ));
      }
   }

   function run( $argv ) {
      $this->getParams( $argv );
      if ( $this->help ) $this->usage();
      $make = vy\Make::load( $this->file );
      $make->run( $this->targets );
   }

   /// paraméterek olvasása
   function getParams( $argv ) {
      try {
         $at = 1;
         while ( $this->getParam( $argv, $at ))
            ;
         if ( ! $this->file )
            $this->file = vy\Make::MAKEVY;
      } catch (Exception $e) {
         $this->usage($e->getMessage());
      }
   }

   /// egy paraméter olvasása
   function getParam( $argv, & $i ) {
      if ( $i >= count($argv))
         return false;
      switch ($a = $argv[$i]) {
         case "-h": case "-?": case "--help":
            $this->help = true;
         break;
         case "-f":
            if ( $this->file )
               throw new Exception("Only one file possible (-f)");
            $this->file = vy\Tools::g( $argv, ++$i );
         break;
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
   function usage( $msg=null ) {
      $ret = [
         "",
         "Usage: php vymake.php <options> [<target>..]",
         "",
         "vymake is a build system which can be used for different",
         "programming languages on many architectures.",
         "",
         "Options:",
         "   -h, --help, -?: show this message",
         "   -f: load makefile from file or url",
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

(new VyMake())->runCli( $argv );
