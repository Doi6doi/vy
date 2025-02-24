<?php

namespace vy;

class GitCli extends Git {

   function executable() { return "git"; }
   
   function clone( $url ) {
      $this->run( "clone %s %s", $this->args(), $this->esc($url) );
   }

   protected function args() {
      $ret = "";
      if ( $d = $this->get( self::DEPTH ))
         $ret .= "--depth $d";
      return $ret;
   }

}
