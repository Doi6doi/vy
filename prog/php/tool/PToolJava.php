<?php

namespace vy;

/// Java fordító ős
class PToolJava extends PToolCompiler {
   
   const
      DEST = "dest",
      SOURCE = "source";
   
   const
      JAR = "jar",
      JAVA = "java",
      JAVAC = "javac";
   
   function __construct() {
      parent::__construct();
	   $this->addFuncs( ["compile","jar",
         "objExt", "run" ] );
   }
   
   function objExt() {
      return ".class";
   }
   
   function compile( $dst, $src=null ) {
      if ( null === $src )
         $src = $dst;
      $this->set( self::EXE, self::JAVAC );
      $this->mlog("compile",Tools::flatten($src));
      $this->exec(
         $this->eArg(),
         $this->srcArg(),
         $this->destArg("-d "),
         $this->warnArg(),
         $this->esc( $src )
      );
   }
      
   function jar( $dst, $src, $main=null ) {
      $this->set( self::EXE, self::JAR );
      $d = $this->get( self::DEST );
      $this->exec("--create",
         "--file=".$this->esc($dst), 
         $main ? "--main-class=".$this->esc($main):"",
         $this->destArg("-C "),
         $this->esc($src)
      );
   }   
   
   /// class vagy jar futtatása
   function run( $dst ) {
      $args = func_get_args();
      $dst = array_shift( $args );
      $this->set( self::EXE, self::JAVA );
      switch ($e = Tools::extension($dst)) {
         case ".jar": 
            array_unshift( $args, "-jar ".$this->esc($dst) );
         break;
         case ".class": 
            if ( $d = $this->destArg("-cp "))
               array_unshift( $args, $d );
            array_unshift( $this->esc($dst) );
         break;
         default:
            throw new EVy("Unknown extension: $e");
      }
      return call_user_func_array( [PToolJava::class,"exec"], $args );
   }
      
   protected function confKind( $fld ) {
      switch ($fld) {
         case self::DEST: return Configable::SCALAR;
         case self::SOURCE: return Configable::ARRAY;
         default: return parent::confKind( $fld );
      }
   }
      
   /// forráskönyvtár kapcsoló
   protected function srcArg() {
      if ( ! $s = $this->get( self::SOURCE ))
         return "";
      if ( ! is_array($s))
         $s = [$s];
      return "-sourcepath ".$this->esc( implode($this->pathSep(),$s));
   }
   
   /// figyelmeztetések kapcsolója
   protected function warnArg() {
      if ( $this->get( self::WARN ))
         return "-Xlint -Werror";
         else return "";
   }
   
   /// célkönyvtár kapcsoló
   protected function destArg($pre) {
      if ( ! $d = $this->get(self::DEST))
         return "";
      return $pre.$this->esc($d);
   }
   
   /// útvonalak elválasztója
   protected function pathSep() {
      if ( Tools::WINDOWS == Tools::system())
         return ";";
         else return ":";
   }
   
}
