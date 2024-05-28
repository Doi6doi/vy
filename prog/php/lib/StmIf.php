<?php

namespace vy;

/// feltételes kifejezés
class StmIf
   extends ExprCtxForward
   implements Stm
{

   const
      ELSE = "else",
      IF = "if";

   protected $cond;
   protected $bif;
   protected $belse;

   function __construct( Block $owner ) {
	  parent::__construct( $owner );
   }

   /// if olvasása
   function read( ExprStream $s ) {
      $s->readWS();
      $s->readToken( self::IF );
      $s->readWS();
      $s->readToken("(");
      $this->cond = $s->readExpr();
      $s->readToken(")");
      $this->bif = new StmBranch( $this );
      $this->bif->read( $s );
      if ( $s->readIf( self::ELSE )) {
		 $this->belse = new StmBranch( $this );
		 $this->belse->read( $s );
      }
   }

   function run( RunCtx $ctx ) {
	  if ( $this->cond->run( $ctx ) )
		 return $this->bif->run( $ctx );
      else if ( $this->belse )
         return $this->belse->run( $ctx );
   }

}

