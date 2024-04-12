<?php

namespace vy;

class Stack {

   protected $owner;
   protected $stream;
   protected $items;

   function __construct( ExprCtx $owner ) {
      $this->owner = $owner;
      $this->items = [];
   }

   function setStream( Stream $s ) {
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

   /// elemek száma
   function count() { return count($this->items); }

   /// műveleti precedencia
   function precedence( $t ) {
      switch ($t) {
         case ":=": return 10;
         case "||": return 20;
         case "&&": return 21;
         case "!": return 22;
         case "=": case "!=": case "<=": case ">=": case "<": case ">":
            return 25;
         case "+": case "-": return 30;
         case "*": case "/": case "%": return 40;
         default: return null;
      }
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
      $s->readWS();
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
      return $this->joinNullary()
         || $this->joinBinary();
   }

   /// nulláris összevonás
   protected function joinNullary() {
      return $this->joinFunc();
   }

   /// bináris összevonás
   protected function joinBinary() {
      return $this->joinInfix();
   }

   /// azonosító kiértékelés
   protected function joinFunc() {
      if ( ! $this->isToken(0) ) return false;
      $t = $this->items[0];
      if ( ! $this->stream->isIdent( $t[0], true )) return false;
      if ( ! $ret = $this->owner->resolve( $t, ExprCtx::FUNC ))
         return false;
      return $this->join( 1, $ret );
   }

   /// következő stream elem
   protected function next() {
      return $this->stream->next();
   }

   /// bináris művelet összevonás
   protected function joinInfix() {
      if ( $this->count() < 3 )
         return;
      if ( ! ($this->isExpr(2) && $this->isToken(1) && $this->isExpr(0)))
         return false;
      $t = $this->items[1];
      if ( $this->precedence( $t ) < $this->precedence( $this->next() ))
         return false;
      return $this->join( 3, new Infix( $t, $this->items[2], $this->items[0] ));
   }

   /// összevonás
   protected function join( $n, $ret ) {
      array_splice( $this->items, 0, $n, [$ret] );
      return true;
   }

   /// token van-e ezen a helyen
   protected function isToken($i) {
      return is_string( $this->items[$i] );
   }

   /// kifejezés van-e ezen a helyen
   protected function isExpr($i) {
      return $this->items[$i] instanceof Expr;
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
