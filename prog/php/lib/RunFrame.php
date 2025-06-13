<?php

namespace vy;

class RunFrame {

   protected $name;
   protected $sub;
   protected $vars;

   function __construct( $name, $sub ) {
	  $this->name = $name;
	  $this->vars = [];
     $this->sub = $sub;
   }

   /// ez alframe-e (pl. for, foreach)
   function sub() { return $this->sub; }

   /// van-e ilyen változó
   function has( $name ) {
      return array_key_exists( $name, $this->vars );
   }
   
   /// változó értéke
   function getVar( $name ) {
	   if ( array_key_exists( $name, $this->vars ))
	      return $this->vars[$name];
	      else throw new EVy("Unknown variable: $name");
   }
   
   /// változó értékének beállítása
   function setVar( $name, $val ) {
	   if ( ! is_string( $name ))
	      throw new EVy("Unknown variable: ".$name);
      $this->vars[ $name ] = $val;
   }

   function dump() {
      $ret = ["FRAME ".$this->name];
      foreach ($this->vars as $k=>$v)
         $ret [] = "$k: ".Tools::flatten($v);
      return $ret;
   }

}
