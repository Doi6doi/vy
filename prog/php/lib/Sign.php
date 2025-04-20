<?php

namespace vy;

/// függvény paraméterek és visszatérési érték
class Sign
   implements ExprCtx
{

   /// tulajdonos
   protected $owner;
   /// típusos függvény
   protected $typed;
   /// argumentumok
   protected $args;
   /// visszatérési érték
   protected $result;

   function __construct( ExprCtx $owner, $typed ) {
      $this->owner = $owner;
      $this->typed = $typed;
      $this->args = [];
      $this->result = [];
   }

   function args() { return $this->args; }

   function result() { return $this->result; }

   function defType() { return $this->owner->defType(); }

   function ownerName() { return $this->owner->name(); }

   /// olvasás
   function read( Stream $s ) {
      $s->readWS();
      if ( $s->readIf("(") ) {
         while ( $this->readArg($s) )
            ;
         $s->readToken(")");
         $s->readWS();
      }
      $this->readResult( $s );
   }

   /// argumentumok értékének beáálítása
   function setArgs( RunCtx $ctx, array $args ) {
	  for ( $i=0; $i < count($this->args); ++$i )
		 $ctx->setVar( $this->args[$i]->name(), Tools::g( $args, $i ));
   }

   /// visszatérési típus olvasása
   function readResult( Stream $s ) {
      if ( ! $this->typed ) return;
      while ( $k = Arg::readKind( $s ) ) {
         $typ = $this->readType( $s );
         $this->result [] = new Arg( $this, null, $typ, $k );
      }
      return $this->result;
   }

   /// visszatérési típus alapérték, ha nincs
   function forceResult() {
      if ( $this->result ) return;
      $this->result [] = new Arg( $this, null, $this->defType(), Arg::DEF );
   }

   /// kompatibilitás ellenőrzése
   function checkCompatible( Sign $other, array $map ) {
      $this->checkCompatibleArr( "arg", $this->args, $other->args(), $map );
      $this->checkCompatibleArr( "result", $this->result, $other->result(), $map );
   }

   /// paraméterlista megfeleltetése
   function inherit( Sign $other, array $map ) {
      $this->args = [];
      foreach ( $other->args() as $oa ) {
         $a = new Arg($this, $oa->name(), Tools::gc( $map, $oa->type() ));
         $this->args [] = $a;
      }
      foreach ( $other->result() as $or ) {
         $r = new Arg($this, null, Tools::gc( $map, $or->type()));
         $this->results [] = $r;
      }
   }

   function checkType( $type ) {
      $this->owner->checkType( $type );
   }

   function canCall( $x ) {
	   return $this->owner->canCall( $x );
   }

   function readType( Stream $s ) {
      $s->readWS();
      if ( Stream::IDENT == $s->nextKind() )
         return $this->owner->readType( $s );
      if ( ! $this->defType() )
         throw $s->notexp("type");
      return $this->defType();
   }

   function blockKind() { return Block::NONE; }

   function resolve( $token, $kind ) { return null; }

   function __toString() { return $this->dump(); }

   function dump() {
      $r = $this->result;
      $r = is_array($r) ? implode("",$r) : $r;
      return sprintf( "(%s):%s", implode(",",$this->args), $r );
   }

   /// tömbelemek kompatibilitás ellenőrzése
   protected function checkCompatibleArr( $kind, array $arr, array $other, array $map ) {
      if ( ($n = count($arr)) != count($other))
         throw $this->notComp( $other, "$kind count");
      for( $i=0; $i<$n; ++$i)
         $arr[$i]->checkCompatible( $other[$i], $map );
   }

   /// argumentum olvasás
   protected function readArg( $s ) {
      $s->readWS();
      if ( ")" == $s->next() )
         return false;
      if ( $this->args )
         $s->readToken(",");
      $ret = new Arg( $this );
      $ret->read( $s, $this->typed, Arg::REF );
      $this->args [] = $ret;
      return true;
   }

   protected function notComp( Sign $other, $reason ) {
      return new EVy("Not compatible sign: ".
         $this->owner()->name().": ".$result );
   }

}
