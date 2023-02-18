<?php

/// kezdeti üzenet
class VSHelo extends VSCommand {

   /// fontos interfészek
   protected $intfs;

   function __construct( array $intfs ) {
      parent::__construct();
      $this->intfs = [];
      foreach ( $intfs as $i )
         $this->intfs[$i->fullName()] = $i->handle();
   }

   function handleValue() { return 1; }

   function toVson() {
      return VSStream::toVson( [$this->handle, $this->intfs] );
   }

}
