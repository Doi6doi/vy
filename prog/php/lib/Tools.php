<?php

namespace vy;

class Tools {

   const
      LINUX = "Linux",
      WINDOWS = "Windows";

   static function g($arr,$fld) {
      if ( is_array($arr) && array_key_exists($fld,$arr))
         return $arr[$fld];
      return null;
   }

   static function gc($arr,$fld) {
      if ( ! is_array($arr) )
         throw new EVy("Not an array");
      if ( ! array_key_exists( $fld, $arr ))
         throw new EVy("Missing key: $fld");
      return $arr[$fld];
   }

   static function debug() {
      $ret = [];
      foreach (func_get_args() as $x) {
         $ret [] = $x;
      }
      fprintf( STDERR, "%s", self::flatten($ret)."\n" );
   }

   static function allErrors() {
      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL);
   }

   static function loadFile( $fname ) {
      if ( false === ($ret = file_get_contents( $fname )))
         throw new EVy("Could not load file: $fname");
      return $ret;
   }

   static function saveFile( $fname, $data ) {
      if ( false === file_put_contents( $fname, $data ))
         throw new EVy("Could not save file: $fname");
   }

   static function jsonDecode( $data ) {
      $ret = json_decode( $data, true );
      self::checkJson();
      return $ret;
   }
   
   static function jsonEncode( $data, $pretty=false ) {
	  $ret = json_encode( $data, $pretty ? JSON_PRETTY_PRINT : 0 );
	  self::checkJson();
	  return $ret;
   }

   static function checkJson() {
      if ( JSON_ERROR_NONE != json_last_error() )
         throw new EVy( "JSON error: ".json_last_error_msg() );
   }

   /// csomagnév útvonallá
   static function pkgDir( $x ) {
      return str_replace( ".","/",$x);
   }

   /// útvonal csomaggá
   static function dirPkg( $x ) {
      return str_replace( "/",".",$x);
   }

   /// fájl kiterjesztése
   static function extension($path) {
      if ( preg_match('#(\.[^./]*)$#', $path, $m ))
         return $m[1];
      return null;
   }

   /// fájl kiterjesztés megváltoztatása
   static function changeExt( $fname, $ext ) {
      if ( preg_match('#^(.*)\.[^./]*$#', $fname, $m ))
         return $m[1].$ext;
      else
         return $fname.$ext;
   }

   /// első betű nagybetű
   static function firstUpper($s) {
	  if ( 0 == strlen($s))
	     return $s;
	  return strtoupper( $s[0] ).substr($s,1);
   }

   /// első betű kisbetű
   static function firstLower($s) {
	  if ( 0 == strlen($s))
	     return $s;
	  return strtolower( $s[0] ).substr($s,1);
   }

   /// futó operációs rendszer
   static function system() {
	  if ( preg_match('#^win#i', PHP_OS))
	     return self::WINDOWS;
	  else if ( preg_match('#^linux#i', PHP_OS ))
	     return self::LINUX;
	  else
	     return PHP_OS;
   }

   /// érték osztállyal   
   static function withClass( $x ) {
	  switch ( $t = gettype($x) ) {
		 case "object": return sprintf("%s (%s)", $x, get_class($x));
		 default: return $x;
	  }
   }
   
   /// tömör exception stack
   static function shortTrace( \Throwable $e ) {
      if ( $p = $e->getPrevious() )
         return self::shortTrace( $p );
      $ret = "";
      $ts = $e->getTrace();
      for ( $i = count($ts)-1; 0<=$i; --$i) {
         $t = $ts[$i];
         $c = self::g( $t, "class" );
         $f = self::g( $t, "file");
         $l = self::g( $t, "line");
         $a = self::g( $t, "args" );
         $ret .= sprintf("%s:%d:   %s%s%s\n", 
            $f ? basename($f): "<unknown>", $l ? $l : "", 
            $c ? "$c::" : "", $t["function"],
            $a ? "(".self::flatten($a,60).")" : "" );
      }
      return $ret;
   }

   /// kiírható forma
   static function flatten( $x, $max=null ) {
      $ret = "";
      if ( is_array($x)) {
         $arr = [];
         foreach ($x as $i)
            $arr [] = self::flatten($i,$max);
         $ret = implode(",",$arr);
      } else if ( is_object($x)) {
         if ( method_exists( $x, "__toString"))
            $ret = "$x";
            else $ret = "?";
      } else {
         $ret = "$x";
      }
      if ( $max && $max < strlen($ret))
         $ret = substr( $ret, 0, $max-2 )."..";
      return $ret;
   }

   static function shortTraceMsg( \Throwable $e ) {   
      return sprintf( "%s\n%s (%s:%s): %s\n",
         self::shortTrace( $e ),
         get_class($e), basename( $e->getFile() ),
            $e->getLine(), $e->getMessage() );
   }

   static function chdir( $dir ) {
      if ( ! chdir( $dir ))
         throw new EVy("Cannot change directory to $dir");
   }
   
}
