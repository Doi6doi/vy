<?php

/// több repot használó repo
class VyRepoMulti extends VyRepo {

   protected $choices;

   function __construct() {
      $this->choices = [];
   }

   /// új elem hozzáadása
   function add( $repo ) {
      if ( ! ( $repo instanceof VyRepo ))
         $repo = VyRepo::create( $repo );
      $this->choices [] = $repo;
   }

   function read( $i, $ver ) {
      foreach ( $this->choices as $c ) {
         if ( $c->contains( $i ))
            return $c->read($i);
      }
      return parent::read( $i );
   }

   function contains( $i, $ver ) {
      foreach ( $this->choices as $c ) {
         if ( $c->contains( $i, $ver ))
            return true;
      }
      return false;
   }

}
