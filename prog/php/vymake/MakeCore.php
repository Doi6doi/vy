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
	   $this->addFuncs(["arch","changeExt","cd","copy",
	      "cwd","dir","dumpStack", "echo","exec","exeExt","exists",
         "exit", "explode","fail","format","getEnv", 
         "implode","isDir","level","loadFile",
         "make","mkdir","older","path","purge","replace",
         "regexp", "saveFile", "setEnv", "setPath", 
         "setPerm", "system", "tool", "which" ]);
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

   /// könyvtár váltás
   function cd($dir) {
      Tools::chdir($dir);
   }

   /// tömbből string
   function implode( $sep, $arr ) {
      return implode($sep,$arr);
   }

   /// eszköz létrehozása
   function tool( $kind, $args=null ) {
      $cname = "vy\\".Tool::TOOL.$kind;
      $ret = new $cname();
      if ( $args )
         $ret->set( $args );
      return $ret;
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
         if ( $d && ! is_dir( $d )) {
            $this->owner->log( Make::INFO, "Creating dir: $d");
            Tools::mkdir( $d );
         }
      }
   }

   /// könyvtár tartalma
   function dir( $path ) {
      if ( ! $path ) $path = ".";
      $ret = [];
      foreach ( scandir($path) as $f ) {
         if ( ! in_array( $f, [".",".."] ))
            $ret [] = $f;
      }
      return $ret;
   }
         
   /// könyvtár-e
   function isDir( $path ) {
      return is_dir($path);
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
      print( Tools::flatten($x)."\n" );
   } 
   
   /// fájl törlés
   function purge( $x ) {
	  if ( is_array( $x )) {
	     foreach ( $x as $i )
	        $this->purge( $i );
	  } else if ( preg_match('#\*#', $x )) {
        foreach ( Tools::glob($x) as $i ) {
           $this->purge( $i );
        }
     } else if ( file_exists( $x )) {
			$this->owner->log( Make::INFO, "Purging $x");
         Tools::purge( $x, true );
	  }
   }

   /// stack kiírása
   function dumpStack() {
      $this->owner->dumpStack();
   }

   /// fájl másolás
   function copy( $src, $dst ) {
	   if ( is_array( $src )) {
	     foreach ( $src as $i )
	        $this->copyToDir( $i, $dst );
      } else if ( preg_match('#\*#', $src )) {
        foreach ( Tools::glob($src) as $i )
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

   /// regexp csere
   function regexp( $src, $rxp, $dst=null ) {
      if ( is_array( $src )) {
         $ret = [];
         foreach ( $src as $s )
            $ret [] = $this->regexp( $s, $rxp, $dst );
         return $ret;
      }
      if ( null == $dst ) {
         preg_match( $rxp, $src, $m );
         return $m;
      } else {
         return preg_replace( $rxp, $dst, $src );
      }
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

   /// aktuális könyvtár
   function cwd() {
	  return getcwd();
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
