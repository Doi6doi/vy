<?php

namespace vy;

// md dox író
class DoxTxtWriter extends DoxWriter {
   
   function typ() { return DoxWriter::TXT; }   

   protected function formatLink( $txt, $lnk ) { 
      $ret = "$txt";
      if ( $lnk && $lnk != $txt && $lnk != "#$txt" )
         $ret .= "($lnk)";
      return $ret;
   }

   protected function formatPart( $part, $m ) {
      switch ( $part ) {
         case self::CODE: 
         case self::EM:
         case self::STRONG:
            return $m[1];
         case self::HEAD:
            return $m[2];
         default:
            return parent::formatPart( $part, $m );
      }
   }


}
