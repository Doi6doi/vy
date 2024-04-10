<?php

class VyInterfType {

   protected $owner;
   protected $name;

   protected $same;

   function __construct( $owner, $name ) {
      $this->owner = $owner;
      $this->name = $name;
      $this->same = [];
   }

   function name() { return $this->name; }

   function readDetails( VyStream $s ) {
      $s->readWS();
      if ( $s->readIf("=") )
         throw new Exception("nyf");
   }

}
