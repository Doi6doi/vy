<?php

namespace vy;

/// alap make függvények
class MakeCore 
   extends MakeImport
{
   const
      CORE = "Core";

   protected $owner;

   /// generálási szabályok
   protected $rules;
   /// függőségek
   protected $depends;

   function __construct( $owner ) {
	  parent::__construct( $owner, self::CORE );
	  $this->rules = [];
	  $this->depends = [];
	  $this->addFuncs(["echo","depend","format","generate",
	     "needGen","replace","system"]);
   }
	  
   /// egy fájl módosítási dátuma
   protected function modified( $fname ) {
	  if ( ! file_exists( $fname ))
	     return null;
	  return filemtime( $fname );
   }

   /// egy fájl generálása
   protected function doGenerate( $fname ) {
	  foreach ( $this->rules as $r ) {
		 if ( $r->matches( $fname ))
		    $r->apply( $fname );
	  }
	  throw new EVy("No rule to generate ".$fname);
   }

   /// függőség hozzáadása
   function depend( $dst, $src ) {
	  if ( ! $dst || ! $src ) {
	     return;
	  } else if ( is_array( $dst )) {
	     foreach ( $dst as $d )
	        $this->depend( $d, $src );
	  } else if ( is_array( $src )) {
	     foreach ( $src as $s )
	        $this->depend( $dst, $s );
	  } else {
		  if ( ! array_key_exists( $dst, $this->depends ))
		     $this->depends[$dst] = [];
		  if ( ! in_array( $src, $this->depends[$dst] ))
		     $this->depends[$dst] [] = $src;
      }
   }

   /// kiírás
   function echo( $x ) {
	  print( "$x\n" );
   } 

   /// szükséges-e generálni
   function needGen( $dest ) {
	  return ! $this->modified( $dest )
         || $this->generateDep( $dest );
   }

   /// egy fájl generálása, ha szükséges
   function generate( $dest ) {
	  if ( $this->needGen( $dest ))
	     $this->doGenerate( $dest );
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


}
