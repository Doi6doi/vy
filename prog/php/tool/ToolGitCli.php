<?php

namespace vy;

class ToolGitCli extends PToolGit {

   function __construct() {
      parent::__construct("git");
   }

   function clone( $url ) {
      $this->mlog("clone",$url);
      $this->exec( "clone", $this->args(), $this->esc($url) );
   }

   protected function args() {
      $ret = "";
      if ( $d = $this->get( ToolGit::DEPTH ))
         $ret .= "--depth $d";
      return $ret;
   }

}
