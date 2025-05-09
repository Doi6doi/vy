<?php

namespace vy;

class Conds 
   extends Block
{

   protected $skip;

   function blockKind() { return Block::COND; }
   
   function read( ExprStream $s ) {
      $this->skip = Skip::mark($s);
      $s->skipBraces();
   }
   
   function readPhase($x) {
      if ( ! $s = Skip::jump($this->skip)) 
         return;
      parent::read( $s );
      $this->skip = null;
   }
   
}
