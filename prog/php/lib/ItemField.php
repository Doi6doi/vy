<?php

namespace vy;

/// elem mező
class ItemField 
   extends Vari
{

   protected $owner;
   protected $type;
   protected $kind;
   protected $value;

   function __construct( Item $owner ) {
      parent::__construct(null);
      $this->owner = $owner;
   }

   function read( ExprStream $s ) {
      $s->readWS();
      $this->name = $s->readIdent();
      $this->readType( $s );
      $this->readValue( $s );
      $s->readTerm();
   }    

   function run( RunCtx $ctx ) {
      return $ctx->thisObj()->getVar( $this->name );
   }

   protected function readType( $s ) {
      if ( $this->updateKind($s) )
         $this->type = $this->owner->readType( $s );
   }
   
   protected function readValue( $s ) {
      $s->readWS();
      if ( ! $s->readIf( "=" )) return;
      $s->push( $this->owner, true );
      $this->value = $s->readExpr();
      $s->pop( true );
   }

}
