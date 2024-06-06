<?php

namespace vy;

class Gcc extends CCompiler {
 
   protected $inc;
 
   function executable() { return "gcc"; }
   
   function depend( $dst, $src ) {
      $this->run( "%s -MM %s > %s", $this->incDirArg(),
         $this->esc( $src ), $this->esc( $dst ));
   }
   
   function linkLib( $dst, $src ) {
      $this->run( "-shared -o %s %s %s %s", $this->esc($dst), 
         $this->esc($src), $this->libDirArg(), $this->libArg() );
   }
 
   function linkPrg( $dst, $src ) {
      $this->run( "-o %s %s %s %s", $this->esc($dst), $this->esc($src),
         $this->libDirArg(), $this->libArg() );
   }
 
   function compile( $dst, $src ) {
      $this->run( "%s -c -fPIC -o %s %s", $this->incDirArg(),
         $this->esc($dst), $this->esc($src));
   }
   
   /// include könyvtár parancssori argumentum
   function incDirArg() { 
      return $this->arrayArg( $this->incDir, "-I " ); 
   }
      
   /// include könyvtár parancssori argumentum
   function libDirArg() {
      return $this->arrayArg( $this->libDir, "-L " );
   }

   /// használt könyvtár parancssori argumentum
   function libArg() {
      return $this->arrayArg( $this->lib, "-l" );
   }      
   
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
}
