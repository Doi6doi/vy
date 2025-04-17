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
Tools::debug("repodir read $pkgName $ver");      
      $ret = null;
      $rpp = $this->root."/".str_replace(".","/",$pkgName);
print("RepoDir.read p:$rpp*.vy");
      foreach ( glob( "$rpp*.vy" ) as $f ) {
print("RepoDir.read f:$f");
         if ( preg_match( '#^'.preg_quote($rpp).'(|@\d+)\.vy$#', $f )) {
            $i = $this->readStream( new ExprStream($f));
            $this->addItem($i);
            if ( Version::better( $i, $ver, $ret ))
               $ret = $i;
         }
      }
      return $ret;
   }

}
