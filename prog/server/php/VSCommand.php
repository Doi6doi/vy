<?php

/// kommunikációs utasítás
abstract class VSCommand extends VSHandled {

   protected $args;

   function __construct() {
      parent::__construct( $this->handleValue() );
      $this->args = [];
   }

   /// a parancs kódja
   abstract function handleValue();

   /// Vson-ná alakítás
   abstract function toVson();

   function handleKind() { return VSC::COMMAND; }


}
