<?php

namespace vy;

class RunFrame {

   protected $name;
   protected $vars;

   function __construct( $name ) {
	  $this->name = $name;
	  $this->vars = [];
   }

   /// van-e ilyen változó
   function has( $name ) {
	  return array_key_exists( $name, $this->vars );
   }
   
   /// változó értéke
   function getVar( $name ) {
	  if ( array_key_exists( $name, $this->vars ))
	     return $this->vars[$name];
	     else throw new EVy("Unknwon variable: $name");
   }
   
   /// változó értékének beállítása
   function setVar( $name, $val ) {
	  if ( ! is_string( $name ))
	     throw new EVy("Unknown variable: ".$name);
      $this->vars[ $name ] = $val;
   }

}
