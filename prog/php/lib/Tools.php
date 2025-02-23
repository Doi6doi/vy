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
   
   /// architektúra
   static function arch() {
      return php_uname("m");
   }

   /// érték osztállyal   
   static function withClass( $x ) {
	  switch ( $t = gettype($x) ) {
		 case "object": return sprintf("%s (%s)", $x, get_class($x));
		 default: return $x;
	  }
   }
   
   /// engedély megadása, vagy törlése
   static function setPerm( $file, $perm="a", $to="a", $on=true ) {
      switch ( $s = self::system() ) {
         case self::LINUX: 
            return self::setLinuxPerm( $file, $perm, $to, $on );
         default:
            throw new EVy("Cannot change permission in system: $s");
      }
   }
   
   /// asszociatív tömb-e
   static function isAssoc( $x ) {
      if ( ! is_array( $x )) return false;
      foreach ( $x as $k=>$v ) {
         if ( 0 === $k ) 
            return false;
            else return true;
      }
      return true;
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

   /// join arguments as path
   static function path() {
      return implode( DIRECTORY_SEPARATOR, func_get_args() );
   }

   /// Create a directory if not exists
   static function mkdir( $dir ) {
      if ( is_dir( $dir )) return;
      if ( ! mkdir( $dir, 0777, true ) )
         throw new EVy("Cannot create directory: $dir");
   }

   /// Copy a file
   static function copy( $src, $dst, $ovr = false ) {
      if ( ! file_exists( $src ))
         throw new EVy("Does not exist: $src");
      if ( $d = is_dir( $dst ))
         $dst = self::path( $dst, basename( $src ));
      if ( ! $ovr && file_exists( $dst ) )
         throw new EVy("Already exists: $dst");
      if ( ! copy( $src, $dst ))
         throw new EVy("Could not copy $src to $dst");
   }

   /// parancs futtatás kimenettel
   static function exec( $cmd, & $rv=null ) {
      $out = [];
      exec( $cmd, $out, $rv );
      return implode( "\n", $out );
   }

   /// bináris megkeresése
   static function which( $bin ) {
      switch ( $s = self::system() ) {
         case self::LINUX:
            $ret = self::exec( "which '$bin'", $rv );
            if ( $rv )
               throw new EVy("Could not find: $bin");
            return $ret;
         case self::WINDOWS:
            $ret = self::exec( "where '$bin'", $rv );
            if ( $rv )
               throw new EVy("Could not find: $bin");
            return $ret;
         default:
            throw new EVy("Cannot find in: $s");
      }
   }

   /// Delete file or directory
   static function purge( $x, $recurse = false ) {
      if ( ! file_exists($x) ) return;
      $b = basename( $x );
      if ( in_array( $b, [".",".."] )) return;
      if ( is_dir($x) ) {
         if ( $recurse ) {
            foreach ( glob( self::path( $x, "*" ) ) as $f )
               self::purge( $f, true );
         }
         if ( ! rmdir( $x ))
            throw new EVy("Cannot delete directory: $x");
      } else {
         if ( ! unlink( $x ))
            throw new EVy("Cannot delete file: $x");
      }
   }
   
   /// Linux engedély beállítás
   protected static function setLinuxPerm( $file, $perm, $to, $on ) {
      if ( false === ($v = fileperms($file)))
         throw new EVy("Cannot get permissions: $file");
      $v &= 0x1ff;
      $m = self::permSubjMask( $perm, $to );
      if ( $on )
         $v |= $m;
         else $v &= ~$m;
      if ( ! chmod( $file, $v ))
         throw new EVy("Cannot change permissions: $file");
   }

   /// engedély maszk
   protected static function permSubjMask( $perm, $to ) {
      $ret = 0;
      $s = self::subjMask( $to );
      $p = self::permMask( $perm );
      for ( $i=0; $i<3; ++$i) {
         if ( $s & (1 << $i) )
            $ret |= ($p << (3*$i));
      }
      return $ret;
   }
   
   /// személy maszk
   protected static function subjMask( $s ) {
      if ( 1 != strlen( $s )) {
         $ret = 0;
         foreach ( $s as $c )
            $ret |= self::subjMask( $c );
         return $ret;
      }
      switch ( $s ) {
         case "u": return 4;
         case "g": return 2;
         case "o": return 1;
         case "a": return 7;
         default: throw new EVy("Unknown subject: $s");
      }
   }
   
   /// jog maszk
   protected static function permMask( $p ) {
      if ( 1 != strlen( $p )) {
         $ret = 0;
         foreach ( $p as $c )
            $ret |= self::permMask( $c );
         return $ret;
      }
      switch ( $p ) {
         case "r": return 4;
         case "w": return 2;
         case "x": return 1;
         case "a": return 7;
         default: throw new EVy("Unknown permission: $p");
      }
   }
   
}
