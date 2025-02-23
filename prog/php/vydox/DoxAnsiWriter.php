<?php

namespace vy;

// md dox író
class DoxAnsiWriter extends DoxWriter {
   
   function typ() { return DoxWriter::TXT; }   

   protected function formatLink( $txt, $lnk ) { 
      $ret = "$txt";
      if ( $lnk && $lnk != $txt && $lnk != "#$txt" )
         $ret .= "($lnk)";
      $ret = $this->esc( $ret, 4 );
      return $ret;
   }

   protected function formatPart( $part, $m ) {
      $e = "\x1b";
      switch ( $part ) {
         case self::CODE: return $this->esc( $m[1], 3 );
         case self::EM: return $this->esc( $m[1], 3 );
         case self::STRONG: return $this->esc( $m[1], 1 );
         case self::HEAD: return $this->esc( " $m[2] ", "17" );
         default:
            return parent::formatPart( $part, $m );
      }
   }

   /// ansi escape
   protected function esc( $s, $fmt ) {
      $fmt = "$fmt";
      $ret = "\x1b[";
      for ( $i=0; $i < strlen($fmt); ++$i ) {
         if ( 0 < $i )
            $ret .= ";";
         $ret .= $fmt[$i];
      }
      $ret .= "m$s\x1b[0m";
      return $ret;
   }

}
