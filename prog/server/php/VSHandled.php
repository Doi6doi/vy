<?php

/// azonosítóval rendelkező objektum
abstract class VSHandled {

   /// azonosító
   protected $handle;

   function __construct( $handle = null ) {
      $this->handle = VSHandle::create( $this, $handle );
   }

   /// azonosító fajta
   abstract function handleKind();

   /// azonosító
   function handle() { return $this->handle; }

}

