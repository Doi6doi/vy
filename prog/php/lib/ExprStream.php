<?php

namespace vy;

class ExprStream
   extends Stream
{

   protected $blocks;
   protected $stacks;

   function __construct( $filename ) {
      parent::__construct( $filename );
      $this->blocks = [];
      $this->stacks = [];
   }

   function push( ExprCtx $b, $newStack ) {
       $this->blocks [] = $b;
       if ( $newStack )
          $this->stacks [] = new Stack( $b );
   }

   /// pontosvesssző olvasása, ha van
   function readTerm() {
      $this->readWS();
      $ret = $this->readIf(";");
      $this->readWS();
      return $ret;
   }

   function pop( $withStack ) {
      array_pop( $this->blocks );
      if ( $withStack )
         array_pop( $this->stacks );
   }

   function top() {
      return end( $this->blocks );
   }

   function stack() {
      if ( ! $ret = end( $this->stacks ) )
         throw new EVy("Stream has no stack");
      return $ret;
   }

   function readExpr() {
      return $this->stack()->readExpr( $this );
   }

   function readStm() {
      $this->readWS();
      $ret = null;
      $n = $this->next();
      $top = $this->top();
      $bkind = $top->blockKind();
      $semi = false;
      switch ( $bkind ) {
         case Block::COND:
            if ( Given::GIVEN == $n )
               $ret = new Given( $top );
         break;
         case Block::BODY:
            switch ( $n ) {
               case StmReturn::RETURN:
                  $ret = new StmReturn();
		  $semi = true;
               break;
               case StmThrow::THROW:
                  $ret = new StmThrow();
                  $semi = true;
               break;
           }
	break;
        case Block::NONE:
           throw new EVy("NONE block cannot have statements");
      }
      switch( $n ) {
         case StmCase::CASE: $ret = new StmCase( $top ); break;
         case StmIf::IF: $ret = new StmIf( $top ); break;
         case StmFor::FOR: $ret = new StmFor( $top ); break;
         case StmForeach::FOREACH: $ret = new StmForeach( $top ); break;
      }
      if ( $ret ) {
         $ret->read( $this );
      } else {
         $ret = $this->readExpr();
         $semi = true;
      }
      if ( $semi )
         $this->readToken(";");
	  return $ret;
   }

   function nextLength() {
      if ( self::SYMBOL == $this->nextKind() ) {
         if ( Oper::cont( $this->nextChar(0), $this->nextChar(1)) )
            return 2;
            else return 1;
      }
      return parent::nextLength();
   }

   /// zárójelezésnek megfelelő nyitó-záró részek átugrása
   function skipBraces() {
      $brs = [];
      $opn = ["{","(","["];
      $cls = ["}",")","]"];
      $this->readWS();
      if ( ! in_array( $this->next(), $opn )) return;
      $brs [] = $this->read();
      while ($brs) {
         if ( $this->eos())
            throw new EVy("End of stream before closing brace");
         $n = $this->read();
         if (in_array($n,$opn)) {
            $brs [] = $n;
         } else if (in_array($n,$cls)) {
            if ( $n != Braced::pair(array_pop( $brs )))
               throw new EVy("Wrong closing brace");
         }
      }
   }

}

