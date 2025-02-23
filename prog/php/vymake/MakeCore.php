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
	  $this->addFuncs(["arch","changeExt","copy",
        "echo","exec","exeExt","exists",
        "explode","fail", "format","getEnv", 
        "implode", "level","loadFile",
        "make","mkdir","older","path", "purge","replace",
        "saveFile", "setEnv", "setPath", "setPerm", 
        "system","which" ]);
      $this->add( "init", new MakeInit( $this ));
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
   
   /// útvonal beállítása
   function setPath( $path ) {
      if ( ! is_array( $path ))
         $path = [$path];
      $path = implode(":",$path);
      $this->appendEnv( "PATH", ":$path" );
      if ( Tools::LINUX == $this->system() )
         $this->appendEnv( "LD_LIBRARY_PATH", ":$path" );
   }
   
   /// egy környezeti változó bővítése
   function appendEnv( $name, $val ) {
      if ( $old = $this->getEnv( $name ))
         $val = $old.$val;
      $this->setEnv( $name, $val );
   }

   /// tömbből string
   function implode( $sep, $arr ) {
      return implode($sep,$arr);
   }

   /// stringből tömb
   function explode( $sep, $arr ) {
      return explode( $sep, $arr );
   }

   /// fájl megkeresése
   function which( $fname ) {
      return Tools::which( $fname );
   }

   /// könyvtár létrehozása, ha nincs
   function mkdir( $ds ) {
      if ( ! is_array($ds) )
         $ds = [$ds];
      foreach ( $ds as $d ) {
         if ( ! is_dir( $d )) {
            $this->owner->log( Make::INFO, "Creating dir: $d");
            Tools::mkdir( $d );
         }
      }
   }

   /// útvonal összeállítás
   function path() {
      return call_user_func_array( [Tools::class, "path"], func_get_args() );
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

   /// létezik-e az összes
   function exists( $f ) {
      if ( ! is_array($f) )
         $f = [$f];
      foreach ( $f as $i ) {
         if ( ! file_exists($i))
            return false;
      }
      return true;
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
         Tools::purge( $x, true );
	  }
   }

   /// fájl másolás
   function copy( $src, $dst ) {
	   if ( is_array( $src )) {
	     foreach ( $src as $i )
	        $this->copyToDir( $i, $dst );
      } else if ( preg_match('#\*#', $src )) {
        foreach ( glob($src) as $i )
           $this->copyToDir( $i, $dst );
      } else {
			$this->owner->log( Make::INFO, "Copying $src -> $dst");
         Tools::copy( $src, $dst, true );
	  }
   }
   
   /// könyvtárba másolás
   protected function copyToDir( $src, $dst ) {
      if ( ! is_dir( $dst ))
         throw new EVy("Not a directory: $dst");
      Tools::copy( $src, $dst, true );
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

   /// engedély kezelés
   function setPerm( $file, $perm="a", $to="a", $on=true ) {
      Tools::setPerm( $file, $perm, $to, $on );
   }

   /// kiterjesztés változtatás
  function changeExt( $fname, $ext ) {
     if ( is_array( $fname )) {
        $ret = [];
        foreach ( $fname as $f)
           $ret [] = $this->changeExt( $f, $ext );
        return $ret;
     }
     if ( $ext && "." != substr( $ext, 0, 1 ))
        $ext = ".$ext";
     if ( preg_match('#^(.*)\\.[^.]*$#', $fname, $m ))
        return $m[1].$ext;
        else return $fname.$ext;
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

   /// architektúra
   function arch() {
      return Tools::arch();
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

   /// összeomlás
   function fail( $msg = null ) {
      throw new EVy( $msg );
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
