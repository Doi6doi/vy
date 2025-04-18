<?php

namespace vy;

/// interfész konstans
class ItemConst
   extends ItemFunc
{

   /// speciális konstans (dec, hex, ...)
   protected $special;
   /// konstans értéke (másik konstans, vagy literál)
   protected $value;

   /// konstans függvény olvasása
   function read( Stream $s ) {
      if ( $s->readIf( "&" ))
         $this->special = true;
      $this->name = $s->readIdent();
      $this->readResult( $s );
      $this->readValue( $s );
      $s->readTerm();
   }

   /// típus név alapján
   protected function itemType() {
      if ( ! $t = $this->sign->result()[0]->type())
         return null;
      return $this->owner->itemType( $t );
   }

   /// eredmény típus olvasása
   protected function readResult( $s ) {
      if ( ! $this->sign->readResult($s) )
         $this->sign->forceResult();
      if ( 1 < count( $this->sign->result() ))
         throw new EVy("Constant can have only one result");
   }

   /// érték olvasása, ha lehet
   protected function readValue( $s ) {
      if ( ! $this->owner->isImplem() ) return;
      $s->readWS();
      if ( ! $s->readIf("=")) return;
      $s->push( $this, true );
      $this->value = $s->readExpr();
      $s->pop( true );
   }

}
