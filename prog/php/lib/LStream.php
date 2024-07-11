<?php

namespace vy;

/// sor olvasó stream
class LStream {

   protected $filename;
   protected $rows;
   protected $row;

   function __construct( $filename ) {
      $this->filename = $filename;
      $data = Tools::loadFile( $filename );
      $this->rows = explode("\n", $data);
      $this->row = 0;
   }
   
   /// stream vége
   function eos() {
	  return $this->row >= count( $this->rows );
   }
   
   /// következő sor
   function read() {
	  if ( $this->eos() )
	     return false;
	  $ret = $this->rows[ $this->row ];
	  ++ $this->row;
	  return $ret;
   }
   
   

}
