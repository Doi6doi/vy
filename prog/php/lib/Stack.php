<?php

namespace vy;

class Stack {

   const
      CLOSERS = [")","]","}"];

   protected $owner;
   protected $stream;
   protected $items;
   protected $braces;

   function __construct( ExprCtx $owner ) {
      $this->owner = $owner;
      $this->items = [];
      $this->braces = [];
   }

   /// kifejezés olvasása
   function readExpr( Stream $s ) {
      $this->clear();
      $this->stream = $s;
      while ( $this->append() ) {
         while ( $this->joinOne() )
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
            return 23;
         case "+": case "-": return 30;
         case "*": case "/": case "%": return 40;
         case "(": case ".": case "[": return 90;
         case ":": return 95;
         default: return null;
      }
   }

   /// literál készítése tokenből, ha lehet
   function makeLiteral( $t ) {
      if ( preg_match('#^\d+$#', $t ))
         $lit = 0+$t;
      else if ( preg_match('#^".*"$#', $t ))
         $lit = Stream::unescape( $t );
      else if ( "true" == $t )
         $lit = true;
      else if ( "false" == $t )
         $lit = false;
      else if ( "null" == $t )
         $lit = null;
      else
         return false;
      return new Literal( $lit );
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
      $tok = $s->read();
      $this->updateBraces( $tok );
      array_unshift( $this->items, $tok );
      $s->readWS();
      return true;
   }

   /// hozzáadható-e ez a token
   protected function allowed( $t ) {
      switch ( $t ) {
         case ";": return false;
         default:
           if ( in_array( $t, self::CLOSERS )) {
              return Braced::pair($t) == end( $this->braces );
           }
           return true;
      }
   }

   /// egy lehetséges összevonás
   protected function joinOne() {
      return $this->joinNullary()
         || $this->joinUnary()
         || $this->joinBinary();
   }

   /// nulláris összevonás
   protected function joinNullary() {
      return $this->joinOper()
         || $this->joinLiteral()
         || $this->joinGlobal()
         || $this->joinName();
   }

   /// unáris összevonás
   protected function joinUnary() {
      return $this->joinBraced( Braced::ROUND )
         || $this->joinBraced( Braced::SQUARE )
         || $this->joinFieldVal()
         || $this->joinHash()
         || $this->joinMember()
         || $this->joinPrefix();
   }

   /// bináris összevonás
   protected function joinBinary() {
      return $this->joinTuple()
         || $this->joinCall()
         || $this->joinIndex()
         || $this->joinInfix();
   }

   /// zárójeles összevonás
   protected function joinBraced( $kind ) {
      if ( Braced::closer($kind) != $this->items[0] )
         return false;
      $op = Braced::opener($kind);
      if ( 2 <= $this->count() && $op == $this->items[1] )
         return $this->join( 2, new Braced($kind) );
      if ( 3 <= $this->count() && $op == $this->items[2] && $this->isExpr(1))
         return $this->join( 3, new Braced($kind, $this->items[1] ));
      return false;
   }

   /// objektum összevonás
   protected function joinHash() {
      if ( "}" != $this->items[0] )
         return false;
      if ( 2 <= $this->count() && "{" == $this->items[1] )
         return $this->join( 2, new Hash() );
      if ( ! $b = $this->hashBody( $this->items[1] ))
         return false;
      if ( 3 <= $this->count() && "{" == $this->items[2] 
         && $b = $this->hashBody( $this->items[1] ))
         return $this->join( 3, new Hash($b));
      return false;
   }

   /// lehet-e ilyen hash törzs
   protected function hashBody($x) {
      $ret = [];
      if ($x instanceof FieldVal) {
         $ret[$x->field] = $x->val;
         return $ret;
      } else if ($x instanceof Tuple) {
         foreach ( $x->items() as $i ) {
            if ( ! ($i instanceof FieldVal))
               return false;
            $ret[$i->field] = $i->val;
         }
         return $ret;
      } else
         return false;
   }

   /// tag összefűzés
   protected function joinMember() {
	  if ( $this->count() < 3 ) return false;
	  $i0 = $this->items[0];
	  if ( ! ($this->isExpr(2) && "." == $this->items[1] 
	     && $this->isToken(0) && $this->stream->isIdent( $i0, true )))
	     return false;
	  return $this->join( 3, new Member($this->items[2], $i0 ));
   }

   /// mező: érték összefűzés
   protected function joinFieldVal() {
      if ($this->count()<3) return false;
      $i2 = $this->items[2];
      if ( ! ( ":" == $this->items[1] && $this->stream->isIdent($i2, true)
         && $this->isExpr(0) ))
         return false;
      $np = $this->precedence( $this->next() );
      if ( $np && $this->precedence(":") > $np ) return false;
      return $this->join( 3, new FieldVal( $i2, $this->items[0] ));
   }


   /// többjegyű operátor
   protected function joinOper() {
      if ( ! ( 2 <= $this->count() && $this->isToken(0) && $this->isToken(1) ))
         return false;
      $t1 = $this->items[1];
      $t0 = $this->items[0];
      if ( ! Oper::cont( $t1, $t0 ))
         return false;
      return $this->join( 2, $t1.$t0 );
   }

   /// azonosító kiértékelés
   protected function joinName() {
      if ( ! $this->isToken(0) ) return false;
      if ( ":" == $this->next() ) return false;
      if ( 1 < $this->count() 
         && in_array( $this->items[1], [".","$"] )) 
         return false;
      $t = $this->items[0];
      if ( ! $this->stream->isIdent( $t[0], true )) return false;
      if ( ! $ret = $this->owner->resolve( $t, ExprCtx::NAME ))
         return false;
      return $this->join( 1, $ret );
   }

   /// globális változó 
   protected function joinGlobal() {
	  if ( ! (2 <= $this->count() 
	     && "$" == $this->items[1]
	     && $this->isToken(0)))
	     return false;
	  $t = $this->items[0];
	  if ( ! $this->stream->isIdent( $t[0], true )) return false;
	  return $this->join( 2, new GlobalVar( $t ));
   }

   /// literál készítés
   protected function joinLiteral() {
      if ( ! $this->isToken(0)) return false;
      if ( ! $ret = $this->makeLiteral( $this->items[0] )) return false;
      return $this->join( 1, $ret );
   }

   /// következő stream elem
   protected function next() {
      return $this->stream->next();
   }

   /// prefix összevonás
   protected function joinPrefix() {
      $n = $this->count();
      if ( $n < 2 ) return false;
      if ( ! ( $this->isExpr(0) && $this->isToken(1))) return false;
      if ( 3 <= $n && $this->isExpr(2)) return false;
      $t = $this->items[1];
      if ( ! Oper::isOper($t, Oper::PREFIX)
            || $this->precedence( $t ) < $this->precedence( $this->next() )
         ) return false;
      
      return $this->join(2, new Prefix($t,$this->items[0]));
   }
   

   /// lista összefűzés
   protected function joinTuple() {
      if ( $this->count() < 3 ) return false;
      if ( ! ( $this->isExpr(2) && "," == $this->items[1] && $this->isExpr(0)))
         return false;
      if ( $this->precedence(",") < $this->precedence($this->next()))
         return false;
      $e2 = $this->items[2];
      $e0 = $this->items[0];
      if ( $e2 instanceof Tuple ) {
         $ret = $e2;
      } else {
         $ret = new Tuple();
         $ret->add( $e2 );
      }
      $ret->add( $e0 );
      return $this->join(3,$ret);
   }


   /// bináris művelet összevonás
   protected function joinInfix() {
      if ( $this->count() < 3 ) return false;
      if ( ! ($this->isExpr(2) && $this->isToken(1) && $this->isExpr(0)))
         return false;
      $t = $this->items[1];
      if ( ! Oper::isOper($t, Oper::INFIX)
         || $this->precedence( $t ) < $this->precedence( $this->next() )
      )
         return false;
      return $this->join( 3, new Infix( $t, $this->items[2], $this->items[0] ));
   }

   /// hívás összevonás
   protected function joinCall() {
      if ( $this->count() < 2 )  return false;
      if ( ! ($this->isExpr(1) && $this->isExpr(0))) return false;
      $e0 = $this->items[0];
      if ( ! $e0 instanceof Braced ) return false;
      if ( Braced::ROUND != $e0->kind() ) return false;
      $e1 = $this->items[1];
      if ( ! $this->owner->canCall( $e1 ) ) return false;
      return $this->join( 2, new Call( $e1, $e0->body()) );
   }

   /// indexelés összevonás
   protected function joinIndex() {
      if ( $this->count() < 2 )  return false;
      if ( ! ($this->isExpr(1) && $this->isExpr(0))) return false;
      $e0 = $this->items[0];
      if ( ! $e0 instanceof Braced ) return false;
      if ( Braced::SQUARE != $e0->kind() || ! $e0->body() ) return false;
      return $this->join( 2, new Indexed( $this->items[1], $e0->body()) );
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

   /// helyes zárójelezés
   protected function updateBraces( $tok ) {
      if ( $p = Braced::pair($tok) ) {
         if ( in_array( $tok, self::CLOSERS ))
            array_pop( $this->braces );
            else array_push( $this->braces, $tok );
      }
   }

}
