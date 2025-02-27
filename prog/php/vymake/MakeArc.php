<?php

namespace vy;

class MakeArc extends MakeImportCmd {
   
   const
      ARC = "Arc";

   protected $arc;

   function __construct( $owner ) {
	  parent::__construct( $owner, self::ARC );
	  $this->arc = new Arc();
      $this->addFuncs( ["extract"] );
   }
   
   function cmd() { return $this->arc; }

   function extract( $src, $dst ) {
	  return $this->arc->extract( $src, $dst );
   }

   
}
