<?php

/// könyvtár repository
class VyRepoDir extends VyRepo {

   protected $root;

   function contains( $i, $ver ) {
      
      $f = str_replace( ".","/",$i);
      return file_exists( sprintf( "%s/%s.vy", $this->root, $f ));
   }


}
