<?php

namespace vy;

/// alap make függvények
class MakeCore {

   protected $owner;

   function __construct( $owner ) {
	  $this->owner = $owner;
	  $this->addAllFuncs();
   }
	  
   /// alap függvények hozzáadása
   protected function addAllFuncs() {
	  $this->addFuncs(["echo","format","generate","system"]);
   }
	     
   /// függvények hozzáadása
   protected function addFuncs( $arr ) {
	  foreach ( $arr as $a )
	     $this->owner->addFunc( $a, [$this,$a] );
   }

   function system() {
	  return Tools::system();
   } 

   function format() {
	  return call_user_func_array( "sprintf", func_get_args() );
   }


}
