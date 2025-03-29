<?php

namespace vy;

class ToolNet extends PToolBase 
{
   
   protected $curl;

   function __construct() {
      parent::__construct();
      $this->curl = new Curl();
      $this->curl->set( Curl::PROGRESS, true );
      $this->addFuncs( ["fetch"] );
   }

   function fetch( $url, $dest = null ) {
      $this->mlog("fetch",$url,$dest);
	   return $this->curl->fetch( [Curl::URL=>$url,
	      Curl::DEST=>$dest] );
   }
   
	protected function logFmt( $meth ) {
      switch ($meth) {
         case "fetch": return "Downloading: %s";
         default: return parent::logFmt($meth);
      }
   }
   
}
