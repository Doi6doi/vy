<?php

namespace vy;

/// hash objektum
class Hash
   implements Expr
{

   protected $body;

   function __construct( $body=null ) {
      if ( is_array( $body ))
         $this->body = $body;
      else
         $this->body = [];
   }

   function body() { return $this->body; }

   function run( RunCtx $ctx ) {
      $ret = [];
      foreach ( $this->body as $k=>$v )
         $ret[$k] = $v->run( $ctx );
      return $ret;
   }

   function __toString() {
      $ret = "";
      foreach ( $this->body as $k=>$v ) {
         if ( $ret ) $ret .= ",";
         $ret .= "$k:$v";
      }
      return "{$ret}";
   }

}
