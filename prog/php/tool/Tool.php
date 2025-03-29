<?php

namespace vy;

interface Tool 
   extends Expr 
{
   const
      TOOL = "Tool";

   function get( $fld );
   
   /// érték beállítása
   function set( $fld, $val=true );
   
}
