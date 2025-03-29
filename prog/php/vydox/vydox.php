<?php


require_once( __DIR__."/../lib/autoload.php" );

vy\Autoload::addPath( __DIR__ );

ini_set('zend.exception_ignore_args', 0);

/// vy documentation tool
class VyDox {

   /// shows full call stack on error
   protected $fullStack;
   /// topic to show help about
   protected $help;
   /// the dox tool
   protected $dox;
   /// input
   protected $src;
   /// output
   protected $dst;

   function __construct() {
      $this->dox = new vy\Dox();
   }

   /// run with arguments and handle exceptions
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

   /// run with arguments
   function run( $argv ) {
      $this->getParams( $argv );
      if ( $this->help ) 
         return $this->help();
      $this->dox->read( $this->src );
      $this->dox->write( $this->dst );
   }

   /// process cli arguments
   function getParams( $argv ) {
      try {
         $at = 1;
         while ( $this->getParam( $argv, $at ))
            ;
         if ( ! $this->src )
            throw new vy\EVy("Source missing");
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
         case "-x":
            $this->fullStack = true;
         break;
         case "-s":
            $a = vy\Tools::g( $argv, ++$i );
            if ( ! preg_match('#(.*?)=(.*)#', $a, $m ))
               throw new vy\EVy("Unknown setting: $a");
            $this->dox->set( $m[1], $m[2] );
         break;
         default:
            if ( ! $this->src )
               $this->src = $a;
            else if ( ! $this->dst )
               $this->dst = $a;
            else
               throw new vy\EVy("Too many arguments");
      }
      ++ $i;
      return true;
   }

/** \name usage
## Usage \var ver

   `vydox <options> <source> <destination>`

   Generates documentation from *<source>* to *<destination>*

### Options

* `-h`: this help
* `-x`: shows full stack trace on error
* `-s <field>=<value>`: sets a dox parameter

### Parameters

*/
   function usage( $msg ) {
      $ud = new vy\Dox();
      $ud->read( __FILE__ );
      $ud->set( vy\Dox::OUTTYPE, vy\DoxWriter::TXT );
      fprintf( STDERR, $ud->writePart("usage", null) );
      $ud->read( __DIR__."/Dox.php" );
      fprintf( STDERR, $ud->writePart("parameters", null) );
      if ( $msg )
         fprintf( STDERR, "$msg\n" );
      exit(1);
   }

}

(new VyDox())->runCli( $argv );

