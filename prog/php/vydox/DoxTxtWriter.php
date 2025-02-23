<?php

namespace vy;

// md dox író
class DoxTxtWriter extends DoxWriter {
   
   function typ() { return DoxWriter::TXT; }   

   /// egy rész formázása
   protected function formatPart( $part, $m ) {
if ( ! array_key_exists( 1, $m ))
      if ( self::LINK == $part )
         return parent::formatPart( $part, $m );
      else if ( array_key_exists( 1, $m ))
         return $m[1];
      else
         return $m[0];
   }

   /// link formázása
   protected function formatLink( $txt, $lnk ) { 
      return sprintf("%s (%s)", $txt, $lnk );
   }


}
