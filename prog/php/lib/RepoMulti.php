<?php

namespace vy;

/// több repot használó repo
class RepoMulti extends Repo {

   protected $choices;

   function __construct() {
      parent::__construct();
      $this->choices = [];
   }

   /// minden elem törlése
   function clear() {
	  $this->choices = [];
   } 

   /// új elem hozzáadása
   function addRepo( $repo ) {
      if ( ! is_array($repo))
         $repo = [$repo];
      foreach ( $repo as $r ) {
         if ( ! ( $r instanceof Repo ))
            $r = Repo::create( $r );
         $this->choices [] = $r;
      }
   }

   function read( $pkgName, Version $ver ) {
      foreach ( $this->choices as $c ) {
         if ( $ret = $c->read( $pkgName, $ver ))
            return $ret;
      }
      return null;
   }

   function find( $pkgName, Version $ver ) {
      foreach ( $this->choices as $c ) {
         if ( $ret = $c->find( $pkgName, $ver ))
            return ret;
      }
      return null;
   }

}
