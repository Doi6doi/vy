<?php

namespace vy;

class StmBranch 
   extends Block
{

   const
      CASE = "case",
      IF = "if";
   
   protected $cond;
   protected $bkind;

   function __construct( $owner ) {
	  parent::__construct( $owner );
	  $this->init();
   }

   function read( ExprStream $s ) {
	  $this->position = $s->position();
	  switch ( $this->bkind ) {
		 case self::IF: break;
		 case self::CASE: $this->readCase( $s ); break;
		 default: throw $this->unBKind();
      }
	  $this->readPart( $s );
   }

   /// illeszkedik-e az ág erre az értékre
   function matches( $val ) {
	  return $this->matchExpr( $this->cond, $val );
   }

   /// illeszkedik-e a kifejezés erre az értékre
   protected function matchExpr( Expr $e, $val ) {
	  if ( $e instanceof Tuple ) {
		 foreach ( $e->items() as $i )
		    if ( $this->matchExpr( $i, $val ))
		       return true;
      } else if ( $e instanceof Interval ) {
		  return $this->litVal( $e->low() ) <= $val
		     && $val <= $this->litVal( $e->high() );
      } else
         return $this->litVal( $e ) == $val;
   }

   /// ág típusának meghatározása
   protected function init() {
	  $o = $this->owner;
	  if ( $o instanceof StmIf )
	     $this->bkind = self::IF;
	  else if ( $o instanceof StmCase )
	     $this->bkind = self::CASE;
	  else {
	     $this->bkind = get_class( $o );
	     throw $this->unBKind();
	  }
   }
	
   /// ismeretlen ág fajta kivétel	
   protected function unBKind() {
	  return new EVy("Unknown branch kind: ".$this->bkind );
   }
	
   /// case feltétel olvasása
   protected function readCase( $s ) {
	  $t = new Tuple();
	  $t->add( $this->readCaseItem( $s ) );
	  while (true) {
	     $s->readWS();
	     if ( ! $s->readIf(",") )
 	        break;
         $ret->add( $this->readCaseItem( $s ) );
      }
      $s->readToken(":");
      $ri = $t->items();
      $this->cond = ( 1 == count( $ri ) ? $ri[0] : $t );
   }
	
   /// case feltétel egy elemének olvasása
   protected function readCaseItem(	$s ) {
	  $t = $s->stack();
	  $s->readWS();
	  if ( ! $lit = $t->makeLiteral( $s->next() ))
	     throw $s->notexp( "literal" );
	  $s->read();
	  $s->readWS();
	  if ( ! $s->readIf("."))
	     return $lit;
      $s->readToken(".");
	  $s->readWS();
      if ( ! $lit2 = $t->makeLiteral( $s->next() ))
	     throw $s->notexp( "literal" );
	  $s->read();
	  return new Interval( $lit, $lit2 );
   }
	
   /// literál értéke
   protected function litVal( $l ) {
	  if ( $l instanceof Literal )
	     return $l->value();
	     else throw new EVy("Literal expected instead of $l");
   }
	
}
