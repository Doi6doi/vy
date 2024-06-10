<?php

namespace vy;

/// parancssori eszköz
abstract class CmdTool {
   
   /// parancsok kiírása
   protected $show;

   /// a futtatható program
   abstract function executable();
   
   /// parancsok és kiment mutatása
   function setShow( $value ) { $this->show = $value; }

   /// fordító futtatása
   protected function run() {
      $args = call_user_func_array( "sprintf", func_get_args() );
      return $this->exec( $this->executable(), $args );
   }

   /// külső program futtatása
   protected function exec( $prg, $args ) {
      $out = [];
      $cmd = sprintf( "%s %s", $this->esc($prg), $args );
      if ( $this->show ) {
         print( "$cmd\n" );
         $r = passthru( $cmd, $rv );
         $outs = "";
      } else {
         $cmd .= " 2>&1";
         $r = exec( $cmd, $out, $rv );
         $outs = implode("\n",$out);
      }
      if ( (false === $r) || (0 != $rv) )
         throw new EVy("Exec error: $rv $outs");
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
   
}
