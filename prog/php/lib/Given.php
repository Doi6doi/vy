<?php

namespace vy;

/// given kifejezés az elő-utófeltétleknél
class Given
   extends Conds
{

   const
      GIVEN = "given";

   /// változók
   protected $vars;

   function __construct( ExprCtx $owner ) {
      parent::__construct( $owner );
      $this->vars = [];
   }

   function kind() { return Block::COND; }

   function defType() { return $this->owner->defType(); }

   function resolve( $token, $kind ) {
      if ( ExprCtx::NAME == $kind ) {
         if ( $ret = Tools::g( $this->vars, $token ))
            return $ret;
      }
      return parent::resolve( $token, $kind );
   }

   /// given olvasása
   function read( Stream $s ) {
      $this->position = $s->position();
      $s->readWS();
      $s->readToken( self::GIVEN );
      $this->readHead( $s );
      $this->readPart( $s );
   }

   /// változó hozzáadása
   function addVar( $name, $type ) {
      if ( array_key_exists( $name, $this->vars ))
            throw new EVy("Duplicate variable: $name");
      $ret = new Arg($this,$name,$type);
      $this->vars[$name] = $ret;
   }

   /// fejrész olvasása
   protected function readHead( $s ) {
      $s->readWS();
      $s->readToken("(");
      $first = true;
      while ( $this->readHeadPart( $s, $first ) )
         ;
      $s->readToken(")");
   }

   /// deklarációs rész olvasása
   protected function readHeadPart( $s, & $first ) {
      $s->readWS();
      if ( ")" == $s->next() )
         return false;
      if ( ! $first ) {
         $s->readToken(";");
         $s->readWS();
      } else
         $first = false;
      $vars = $s->readIdents(",");
      $s->readWS();
      if ( $s->readIf(":") )
         $type = $this->owner->readType( $s );
      else if ( ! $type = $this->defType() )
         throw $s->notexp( "type" );
      foreach ( $vars as $v )
         $this->addVar( $v, $type );
      return true;
   }

}
