<?php

/// kommunikációs utasítás
abstract class VSCommand extends VSHandled {

   function __construct() {
      parent::__construct( $this->handleValue() );
   }

   /// a parancs kódja
   abstract function handleValue();

   /// Vson-ná alakítás
   abstract function toVson();

   function handleKind() { return 1; }


}
