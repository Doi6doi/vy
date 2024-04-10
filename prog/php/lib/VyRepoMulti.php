<?php

/// több repot használó repo
class VyRepoMulti extends VyRepo {

   protected $choices;

   function __construct() {
      parent::__construct();
      $this->choices = [];
   }

   /// új elem hozzáadása
   function add( $repo ) {
      if ( ! ( $repo instanceof VyRepo ))
         $repo = VyRepo::create( $repo );
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
