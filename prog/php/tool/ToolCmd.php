<?php

namespace vy;

/// parancssoros eszköz importja
class ToolCmd extends PToolBase {

   const
      EXE = "exe",
      SHOW = "show";

   function __construct($exe=null) {
      parent::__construct();
      $this->addFuncs(["exec"]);
      $this->set( self::EXE, $exe );
   }

   function exe() {
      if ( ! $ret = $this->get( self::EXE ))
         throw new EVy("exe not set");
      return $ret;
   }

   /// futtatás paraméterekkel
   function exec() {
      $args = implode(" ",func_get_args());
      $out = [];
      $cmd = sprintf( "%s %s", $this->esc($this->exe()), $args );
      if ( $this->get( self::SHOW ) ) {
         print( "$cmd\n" );
         $r = passthru( $cmd, $rv );
         $outs = "";
      } else {
         $cmd .= " 2>&1";
         $r = exec( $cmd, $out, $rv );
         $outs = implode("\n",$out);
      }
      if ( (false === $r) || (0 != $rv) )
         throw new EVy("\nExec error: $rv $outs");
      return $outs;
   }

   /// parancssori escape
   protected function esc( $x ) {
      if ( ! $x )
         return "";
      if ( ! is_array( $x ))
         $x = [$x];
      $ret = "";
      foreach ( $x as $i )
         $ret .= " ".escapeshellarg($i);
      return trim($ret);
   }

   protected function confKind( $fld ) {
      switch ( $fld ) {
         case self::EXE: 
            return Configable::SCALAR;
         case self::SHOW: 
            return Configable::BOOL;
         default: return Configable::NONE;
      }
   }



}
