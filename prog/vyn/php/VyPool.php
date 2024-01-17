<?php

class VyPool {

   protected $items;

   function __construct() {
      $this->items = [];
   }

   function clear() {
      $this->items = [];
   }

   function load( VyStream $s ) {
      $this->clear();
      $s->readToken("\x4f");
      $n = $s->readInt();
      for ( ; 0<$n; --$n )
         $this->items [] = $s->readAny();
   }

}
