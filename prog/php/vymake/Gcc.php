<?php

namespace vy;

class Gcc extends CCompiler {
 
   protected $inc;
 
   function executable() { return "gcc"; }
   
   function depend( $dst, $src ) {
      $this->run( "-MM %s > %s", $this->esc( $src ), $this->esc( $dst ));
   }
   
   function linkLib( $dst, $src ) {
      $this->run( "-shared -o %s %s", $this->esc($dst), $this->esc($src));
   }
 
   function compile( $dst, $src ) {
      $this->run( "%s-c -fPIC -o %s %s", $this->incArg(),
         $this->esc($dst), $this->esc($src));
   }
   
   function setIncDir( $x ) {
      if ( ! $x )
         return $this->inc = [];
      if ( ! is_array( $x ))
         $x = [$x];
      $this->inc = $x;
   }
   
   /// include könyvtár parancssori argumentum
   function incArg() {
      if ( ! $this->inc ) return "";
      return sprintf("-I %s ", implode(":",$this->inc));
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
