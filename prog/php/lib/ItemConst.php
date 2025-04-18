<?php

namespace vy;

/// interfész konstans
class ItemConst
   extends ItemFunc
{

   /// speciális konstans (dec, hex, ...)
   protected $special;

   /// konstans függvény olvasása
   function read( Stream $s ) {
      if ( $s->readIf( "&" ))
         $this->special = true;
      $this->name = $s->readIdent();
      if ( ! $this->sign->readResult($s) )
         $this->sign->forceResult();
      $s->readTerm();
   }

}
