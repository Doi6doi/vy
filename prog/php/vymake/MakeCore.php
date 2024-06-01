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
	  $this->addFuncs(["echo","format","older",
	     "level","purge","replace","system"]);
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
	  return $this->newerThan( $ot, $src );
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
	  } else {
		 if ( file_exists( $x )) {
			$this->owner->log( Make::INFO, "Purging $x");
			unlink( $x );
	      }
	  }
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

   function system() {
	  return Tools::system();
   } 

   function format() {
	  return call_user_func_array( "sprintf", func_get_args() );
   }

   /// egy időpontnál van újabb fájl, vagy valamelyik nincs
   protected function newerThan( $at, $f ) {
	  if ( ! $f )
	     return false;
	  if ( is_array( $f )) {
		 foreach ( $f as $i )
		    if ( $this->newerThan( $at, $i ))
		       return true;
		 return false;
      }
      $ft = $this->modified( $f );
      return ! $ft || $ft > $at;
   }
   
}
