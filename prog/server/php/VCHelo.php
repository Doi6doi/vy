<?php

/// kezdeti üzenet
class VCHelo extends VSCommand {

   function __construct( array $intfs, array $funcs ) {
      parent::__construct();
      $is = [];
      foreach ( $intfs as $i )
         $this->intfs[$i->fullName()] = $i->handle();
      $this->funcs = [];
      foreach (
   }

   function handleValue() { return VSC::HELO; }

   function toVson() {
      return VSStream::toVson( [$this->handle, $this->intfs] );
   }

}
