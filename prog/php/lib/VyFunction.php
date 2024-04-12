<?php

/// interfész függvény
class VyFunction
   implements VyExpr, VyExprCtx
{

   /// tulajdonos
   protected $owner;
   /// név
   protected $name;
   /// konstans
   protected $cons;
   /// argumentumok
   protected $sign;
   /// visszatérési érték
   protected $result;
   /// művelet
   protected $oper;

   function __construct( VyExprCtx $owner ) {
      $this->owner = $owner;
      $this->sign = new VySign( $this );
   }

   function name() { return $this->name; }

   function oper() { return $this->oper; }

   function checkType( $type ) {
      $this->owner->checkType( $type );
   }

   function readType( VyStream $s ) {
      return $this->owner->readType( $s );
   }

   function resolve( $token, $kind ) {
      return null;
   }

   function __toString() { return $this->name()."()"; }

   function read( VyStream $s ) {
      $this->name = $s->readIdent();
      $this->sign->read( $s );
      $s->readWS();
      if ( "{" == $s->next() )
         $this->readDetails( $s );
         else $s->readToken(";");
   }

   /// konstans függvény olvasása
   function readConst( VyStream $s ) {
      $this->cons = true;
      $pre = $s->readIf("&") ? "&" : "";
      $this->name = $pre.$s->readIdent();
      $this->sign->readResult( $s );
      $s->readWS();
      $s->readToken(";");
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
         case VyOper::PREFIX:
         case VyOper::POSTFIX:
         case VyOper::INFIX:
            return $this->readOper($s);
         default:
            throw $s->notexp("detail");
      }
   }

   /// infix kifejezés olvasása
   protected function readOper( $s ) {
      if ( $this->oper )
         throw EVy("Operator already defined");
      $this->oper = new VyOper( $this );
      $this->oper->read( $s );
      return true;
   }


}
