<?php

namespace vy;

/// könyvtár repository
class RepoDir extends Repo {

   protected $root;

   function __construct( $root ) {
      parent::__construct();
      $this->root = $root;
   }

   function read( $pkgName, Version $ver ) {
      $ret = null;
      $rpp = $this->root."/".str_replace(".","/",$pkgName);
      foreach ( glob( "$rpp*.vy" ) as $f ) {
         if ( preg_match( '#^'.preg_quote($rpp).'(|@\d+)\.vy$#', $f )) {
            $i = $this->readStream( new ExprStream($f));
            $this->addItem($i);
            if ( Version::better( $i->ver(), $ver, $ret ))
               $ret = $i;
         }
      }
      return $ret;
   }

}
