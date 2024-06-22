<?php

namespace vy;

/// alap make függvények
class MakeCore 
   extends MakeImport
{
   const
      CORE = "Core";

   protected $owner;

   function __construct( $owner ) {
	  parent::__construct( $owner, self::CORE );
	  $this->rules = [];
	  $this->addFuncs(["echo","exec","exeExt","format","getEnv",
	     "level","loadFile","make","older","purge","replace",
        "saveFile", "setEnv", "system"]);
   }
	  
   /// környezeti változó lekérése
   function getEnv( $name ) {
      $ret = getenv( $name );
      return false === $ret ? "" : $ret;
   }
     
   /// környezeti változó beállítása
   function setEnv( $name, $val ) {
      putenv( "$name=$val" );
   }
     
   /// shell parancs futtatás
   function exec( $cmd ) {  
      passthru( $cmd, $rv );
      return $rv;
   }
     
   /// egy fájl módosítási dátuma
   protected function modified( $fname ) {
	  if ( ! file_exists( $fname ))
	     return null;
	  return filemtime( $fname );
   }

   /// a cél régebbi, mint a feltétlek
   function older( $dst, $src=null ) {
	  if ( ! $ot = $this->modified( $dst ))
	     return true;
     $ret = $this->newerThan( $ot, $src );
//Tools::debug("older $dst", $src, $ret ? "+":"-");      
	  return $ret;
   }

   /// kiírás
   function echo( $x ) {
	  if ( is_array( $x )) {
	     foreach ( $x as $i )
	        $this->echo( $i );
	  } else {
	     print( "$x\n" );
	  }
   } 
   
   /// fájl törlés
   function purge( $x ) {
	  if ( is_array( $x )) {
	     foreach ( $x as $i )
	        $this->purge( $i );
	  } else if ( preg_match('#\*#', $x )) {
        foreach ( glob($x) as $i )
           $this->purge( $i );
     } else if ( file_exists( $x )) {
			$this->owner->log( Make::INFO, "Purging $x");
			unlink( $x );
	  }
   }

   /// make futtatása másik könyvtárban
   function make( $dir, $target = [] ) {
      $this->owner->log( Make::INFO, "Changing directory -> $dir");
      $save = getcwd();
      Tools::chdir( $dir );
      if ( ! is_array( $target ))
         $target = [null,$target];
      (new \VyMake())->run( $target );
      Tools::chdir( $save );
   }
   
   /// naplózási szint
   function level( $v ) {
      $this->owner->log( Make::INFO, "Log level: $v");
      $this->owner->setLevel( $v );
   }

   /// string csere
   function replace( $s, $src, $dst ) {
	  return str_replace( $src, $dst, $s );
   } 

   /// fájl mentése
   function saveFile( $f, $data ) {
      return Tools::saveFile( $f, $data );
   }

   /// fájl betöltése
   function loadFile( $f ) {
      return Tools::loadFile( $f );
   }

   /// rendszer neve
   function system() {
	  return Tools::system();
   } 

   /// futtatható fájl kiterjesztése
   function exeExt() {
      switch ( $s = Tools::system() ) {
         case Tools::WINDOWS: return ".exe";
         case Tools::LINUX: return "";
         default:
            throw new EVy("Unknown system: $s");
      }
   }

   function format() {
	  return call_user_func_array( "sprintf", func_get_args() );
   }

   /// egy időpontnál van újabb fájl, vagy valamelyik nincs
   protected function newerThan( $at, $f ) {
	   if ( ! $f )
	      return false;
	   if ( is_array( $f )) {
		   foreach ( $f as $i ) {
		      if ( $this->newerThan( $at, $i ))
		         return true;
         }
		   return false;
      }
      $ft = $this->modified( $f );
      return $ft && $ft > $at;
   }
   
}
