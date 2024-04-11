<?php


class VyStack {

   protected $owner;
   protected $stream;
   protected $items;

   function __construct( VyExprCtx $owner ) {
      $this->owner = $owner;
      $this->items = [];
   }

   function setStream( VyStream $s ) {
      $this->stream = $s;
   }

   /// kifejezés olvasása
   function readExpr() {
      $this->clear();
      while ( $this->append() ) {
         while ( $this->joinOne())
            ;
      }
      if ( 1 == count( $this->items ) ) {
         if ( $this->isExpr(0) )
            return array_pop( $this->items );
      }
      throw new EVy("Could not read expr: ".$this->dump());
   }

   /// elemk törlése
   protected function clear() {
      $this->items = [];
   }

   /// új token hozzáadása
   protected function append() {
      $s = $this->stream;
      $s->readWS();
      if ( $s->eos() || ! $this->allowed( $s->next() ))
         return false;
      array_unshift( $this->items, $s->read() );
      return true;
   }

   /// hozzáadható-e ez a token
   protected function allowed( $t ) {
      switch ( $t ) {
         case "}": case ";": return false;
         default: return true;
      }
   }

   /// egy lehetséges összevonás
   protected function joinOne() {
      return $this->joinNullary();
   }

   /// nulláris összevonás
   protected function joinNullary() {
      return $this->joinIdent();
   }

   /// azonosító kiértékelés
   protected function joinIdent() {
      if ( ! $this->isToken(0) ) return false;
      $t = $this->items[0];
      if ( ! $this->stream->isIdent( $t[0], true )) return false;
      if ( ! $ret = $this->owner->resolve( $t ))
         throw new EVy("Unknown identifier: $t");
      return $this->join( 1, $ret );
   }

   /// token van-e ezen a helyen
   protected function isToken($i) {
      return is_string( $this->items[$i] );
   }

   /// verem tartalom
   protected function dump() {
      $ret = "";
      for ( $i= count($this->items)-1; 0 <= $i; --$i) {
         if ( $ret )
            $ret .= ",";
         $ret .= $this->items[$i];
      }
      return trim( $ret );
   }

}
