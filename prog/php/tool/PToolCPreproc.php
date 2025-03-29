<?php

namespace vy;

class PToolCPreproc {
	
   protected $defs;
   protected $idefs;
   protected $incDir;
   protected $sysInc;
   
   function __construct() {
	   $this->idefs = [];
	   $this->defs = [];
	   $this->incDir = [];
	   $this->sysInc = [];
   }

   /// include könyvtárak beállítása
   function setIncDir( $val ) {
	  if ( ! is_array( $val ) )
	     $val = $val ? [$val] : [];
	  $this->incDir = $val;
   }

   /// rendszer include könyvtár beállítás
   function setSysInc( $val ) {
	  if ( ! is_array( $val ) )
	     $val = $val ? [$val] : [];
	  $this->sysInc = $val;
   }
	   

   /// függőségek kinyerése
   function depend( $dst, $src, $ext ) {
	  if ( ! is_array( $src ))
	     $src = [$src];
	  $os = new OStream( $dst );
      foreach ($src as $s) {
		 $d = $this->dependOne( $s );
		 $e = Tools::changeExt( $s, $ext ); 
		 $os->writel( "%s: %s %s", $e, $s, implode(" ",$d));
	  }
	  $os->close();
   }	  

   /// függőség fájl olvasása
   function loadDep( $fname ) {
      $ret = [];
      $data = Tools::loadFile( $fname );
      $rows = explode("\n",$data);
      $dst = null;
      foreach ($rows as $r) {
         if ( ! $r ) break;
         if ( ! preg_match('#^((.*):)?(.*?)(\\\\?)$#', $r, $m ))
            throw new EVy("Unknown dep line: $r");
         if ( $m[2] ) {
            $dst = $m[2];
            $ret[$dst] = [];
         }
         $ret[$dst] = array_merge( $ret[$dst], explode(" ", $m[3] ) );
         if ( ! $m[4] )
            $dst = null;
     }
     return $ret;
   }

   /// egy fájl függőségeinek kinyerése
   protected function dependOne( $f ) {
	  $ret = [];
	  $this->defs = $this->idefs;
	  $this->dependStream( new LStream($f), null, $ret );
	  return $ret; 
   }

   /// egy stream függőségeinek kinyerése
   protected function dependStream( $s, $cond, & $ret ) {
	  while ( ! $s->eos() ) {
		 $l = $s->read();
		 if ( false === $cond ) {
		    if ( preg_match( '/\s#endif/', $l, $m ))
		       return;
		 } else {
            if ( preg_match('/^\s*#include\s+("|<)(.*?)("|>)/', $l, $m )) {
		       $this->dependInclude( $m[2], $ret );
	        } else if ( preg_match( '/\s*#define\s+(\S+)\s+(.*?)\s*$/', $l, $m )) {
			   $this->defs[ $m[1] ] = $m[2];
	        } else if ( preg_match( '/\s*#ifdef\s+(\S+)/', $l, $m )) {
			   $def = array_key_exists( $m[1], $this->defs );
   			   $this->dependStream( $s, $def, $ret );
	        } else if ( preg_match( '/\s*#ifndef\s+(\S+)/', $l, $m )) {
		   	   $def = array_key_exists( $m[1], $this->defs );
			   $this->dependStream( $s, ! $def, $ret );
   		    } else if ( preg_match( '/\s#else/', $l, $m )) {
			   if ( $cond )
			      return $this->dependStream( $s, false, $ret );
			   else
	   	          throw new EVy("#else without #if");
   		    } else if ( preg_match( '/\s#endif/', $l, $m )) {
	   	       throw new EVy("#endif without #if");
	        }
	     }
	  }
   }
   
   /// egy include függőségei
   protected function dependInclude( $f, & $ret ) {
	   $dirs = array_merge( ["."], $this->incDir );
	   foreach ( $dirs as $d ) {
	 	  $df = "." == $d ? $f : "$d/$f";
		  if ( file_exists( $df )) {
			  if ( ! in_array( $df, $ret ))
			     $ret [] = $df; 
		     return $this->dependStream( new LStream($df), null, $ret ); 
		  }
	  }
	  foreach ( $this->sysInc as $d ) {
		  if ( file_exists( "$d/$f" )) 
		     return;
      }
      throw new EVy("Include file not found: $f");
   }
	
}
