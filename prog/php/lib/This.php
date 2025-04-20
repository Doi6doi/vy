<?php

namespace vy;

/// this kulcsszó
class This 
   implements Expr
{
   
   function run( RunCtx $ctx ) {
      return $ctx->thisObj();
   }
   
   function __toString() { return "<this>"; }
   
}
