<?php

namespace vy;

class MakeNet 
   extends MakeImportCmd 
{
   
   const
      NET = "Net";

   protected $curl;

   function __construct( $owner ) {
	  parent::__construct( $owner, self::NET );
      $this->curl = new Curl();
      $this->curl->set( Curl::PROGRESS, true );
      $this->addFuncs( ["fetch"] );
   }

   function cmd() { return $this->curl; }

   function fetch( $url, $dest = null ) {
	  return $this->curl->fetch( [Curl::URL=>$url,
	     Curl::DEST=>$dest] );
   }
   
	     
	  
	  
	  
   
   
}
