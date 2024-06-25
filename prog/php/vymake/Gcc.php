<?php

namespace vy;

class Gcc extends CCompiler {
 
   function executable() { return "gcc"; }
   
   function depend( $dst, $src ) {
      $this->run( "%s -MM %s > %s", $this->incDirArg(),
         $this->esc( $src ), $this->esc( $dst ));
   }
   
   function link( $dst, $src ) {
      $this->run( "%s -o %s %s %s %s", $this->modeLinkArg(), 
         $this->esc($dst), $this->esc($src), $this->libDirArg(), 
         $this->libArg() );
   }
 
   function compile( $dst, $src ) {
      $this->run( "%s %s %s -c %s -o %s %s", $this->warnArg(), 
         $this->debugArg(), $this->modeCompArg(), $this->incDirArg(), 
         $this->esc($dst), $this->esc($src)
      );
   }
   
   /// mód argumentum linkelésnél
   function modeLinkArg() {
      return $this->libMode ? "-shared" : "";
   }
   
   /// debug argumentum
   function debugArg() {
      return $this->debug ? "-g": "";
   }
   
   /// warning argumentum
   function warnArg() {
      return $this->warn ? "-w -Werror": "";
   }
   
   /// mód argumentum fordításnál
   function modeCompArg() {
      return $this->libMode ? "-fPIC" : "";
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
