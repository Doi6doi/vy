<?php

namespace vy;

class GitCli extends Git {

   function executable() { return "git"; }
   
   function clone( $url ) {
      $this->run( "clone %s", $this->esc($url) );
   }
   
}
