<?php

namespace vy;

class Pool {

   protected $items;

   function __construct() {
      $this->items = [];
   }

   function clear() {
      $this->items = [];
   }

   function load( Stream $s ) {
      $this->clear();
      $s->readToken("\x4f");
      $n = $s->readInt();
      for ( ; 0<$n; --$n )
         $this->items [] = $s->readAny();
   }

}
