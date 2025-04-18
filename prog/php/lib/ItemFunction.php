<?php

namespace vy;

/// interfész függvény
class ItemFunction
   extends ItemFunc
{

   const
      OPER = "oper";

   /// operátor
   protected $oper;

   function oper() { return $this->oper; }

   /// egy részlet olvasása
   protected function readDetail( $s ) {
      switch ( $s->next() ) {
         case self::OPER: return $this->readOper($s);
         default: return parent::readDetail($s);
      }
   }

   /// infix kifejezés olvasása
   protected function readOper( $s ) {
      if ( $this->oper )
         throw EVy("Operator already defined");
      $s->readToken( self::OPER );
      $this->oper = new Oper( $this );
      $this->oper->read( $s );
      $s->readTerm();
   }

   /// jellemzők öröklése
   function inherit( $other, $map ) {
      parent::inherit( $other, $map );
      if ( $o = $other->oper() ) {
         $this->oper = new Oper();
         $this->oper->inherit( $o );
      }
   }
   
   function checkCompatible( $other, $map ) {
      parent::checkCompatible( $other, $map );
      if ( $other->oper ) {
         if ( ! $this->oper )
            throw $this->notComp( $other, "oper");
         $this->oper->checkCompatible( $other->oper );
      }
   }   

}
