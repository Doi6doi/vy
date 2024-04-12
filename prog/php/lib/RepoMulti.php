<?php

namespace vy;

/// több repot használó repo
class RepoMulti extends Repo {

   protected $choices;

   function __construct() {
      parent::__construct();
      $this->choices = [];
   }

   /// új elem hozzáadása
   function add( $repo ) {
      if ( ! ( $repo instanceof Repo ))
         $repo = Repo::create( $repo );
      $this->choices [] = $repo;
   }

   function force( $x, $ver ) {
      foreach ( $this->choices as $c ) {
         if ( $c->contains($x, $ver))
            return $c->force($x, $ver);
      }
      throw new EVy("Item not found: $x$ver");
   }

   function contains( $x, $ver ) {
      foreach ( $this->choices as $c ) {
         if ( $c->contains( $i, $ver ))
            return true;
      }
      return false;
   }

}
