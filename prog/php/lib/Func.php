<?php

namespace vy;

/// interfész függvény
class Func
   extends ExprCtxForward
   implements Expr
{

   /// név
   protected $name;
   /// konstans
   protected $cons;
   /// argumentumok
   protected $sign;
   /// művelet
   protected $oper;

   function __construct( ExprCtx $owner, $name = null ) {
	  parent::__construct( $owner );
      $this->name = $name;
      $this->sign = new Sign( $this, true );
   }

   function name() { return $this->name; }

   function oper() { return $this->oper; }

   function cons() { return $this->cons; }

   function sign() { return $this->sign; }

   function resolve( $token, $kind ) {
      return null;
   }

   function __toString() { return "<".$this->name().">"; }

   function read( Stream $s ) {
      $this->name = $s->readIdent();
      $this->sign->read( $s );
      $s->readWS();
      if ( "{" == $s->next() )
         $this->readDetails( $s );
         else $s->readToken(";");
   }

   /// konstans függvény olvasása
   function readConst( Stream $s ) {
      $this->cons = true;
      $pre = $s->readIf("&") ? "&" : "";
      $this->name = $pre.$s->readIdent();
      if ( ! $this->sign->readResult( $s ) )
         throw $s->notexp("result");
      $s->readWS();
      $s->readToken(";");
   }

   /// jellemzők öröklése
   function inherit( $other, $map ) {
      $this->name = $other->name();
      $this->cons = $other->cons();
      if ( $o = $other->oper() ) {
         $this->oper = new Oper($this);
         $this->oper->inherit( $o );
      }
      $this->sign->inherit( $other->sign(), $map );
   }

   /// kompatibilitás ellenőrzése
   function checkCompatible( $other, $map ) {
      if ( $this->cons() != $other->cons() )
         throw $this->notComp( $other, "const");
      if ( ! Oper::same( $this->oper(), $other->oper() ))
         throw $this->notComp( $other, "oper");
      $this->sign()->checkCompatible( $other->sign(), $map );
   }

   /// kompatibilitási hiba
   function notComp( $other, $reason ) {
      throw new EVy(sprintf( "function %s (%s->%s) not compatible: %s",
         $this->name(), $other->owner()->name(), $this->owner()->name(),
         $reason ));
   }

   /// további részletek olvasása
   protected function readDetails( $s ) {
      $s->readToken("{");
      while ( $this->readDetail( $s ))
         ;
      $s->readToken("}");
   }

   /// egy részlet olvasása
   protected function readDetail( $s ) {
      $s->readWS();
      switch ( $s->next() ) {
         case "}":
            return false;
         case Oper::PREFIX:
         case Oper::POSTFIX:
         case Oper::INFIX:
            return $this->readOper($s);
         default:
            throw $s->notexp("detail");
      }
   }

   /// infix kifejezés olvasása
   protected function readOper( $s ) {
      if ( $this->oper )
         throw EVy("Operator already defined");
      $this->oper = new Oper( $this );
      $this->oper->read( $s );
      return true;
   }


}
