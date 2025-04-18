<?php

namespace vy;

class ItemType 
   implements Expr
{

   /// tulajdonos
   protected $owner;
   /// név
   protected $name;
   /// egyezők
   protected $same;

   function __construct( Item $owner, $name = null ) {
      $this->owner = $owner;
      $this->name = $name;
      $this->same = [];
   }

   function name() { return $this->name; }

   function owner() { return $this->owner; }

   function read( Stream $s ) {
      while ( $this->readItem( $s ) )
         ;
      if ( ! $this->name )
         throw $s->notexp("type");
      $s->readToken(";");
   }

   function run( RunCtx $r ) { return $this; }

   function same() { return $this->same; }

   function add( $s ) {
      if ( ! in_array( $s, $this->same ))
         $this->same [] = $s;
   }

   function append( ItemType $other ) {
	  foreach ( $other->same() as $s )
	      $this->add( $s );
   }

   function remove( $s ) {
      if ( false === ($i = array_search( $s, $this->same )))
         return false;
      array_splice( $this->same, $i, 1 );
      return true;
   }

/*
   /// név feloldása a típuson belül
   function resolve( $token, $kind ) {
Tools::debug("ItemType.resolve $token $kind");
      foreach ( $this->same as $s ) {
Tools::debug($s);
         $s = explode(".",$s);
         if ( $i = $this->owner->resolve( $s[0], ExprCtx::ITEM )) {
Tools::debug($i);
            if ( $c = $i->resolve( $s[1], $kind ))
               return $c;
         }
      }
      return null;
   }
*/ 
   /// interfész módosítása az átnevezésekkel
   function updateInterf() {
      $ptn = ".".$this->name;
      $lpn = strlen( $ptn );
      for ( $i = count($this->same)-1; 0 <= $i; --$i ) {
         $s = $this->same[$i];
         if ( substr( $s, -$lpn ) != $ptn )
            $this->owner->removeType( $s );
      }
   }

   function __toString() {
      return $this->name().":".implode("=",$this->same);
   }

   protected function readItem( $s ) {
      $s->readWS();
      if ( ! $this->name ) {
         $arr = $s->readIdents(".");
         $n = count($arr);
         $this->name = $arr[ $n-1 ];
         if ( 1 < $n )
            $this->same [] = implode(".",$arr);
         return true;
      } else if ( $s->readIf("=")) {
         $s->readWS();
         $arr = $s->readIdents(".");
         $this->same [] = implode(".",$arr);
         return true;
      } else
         return false;
   }

}
