<?php

namespace vy;

/// interfész leíró fájl
class Interf 
   extends Item
{

   const
      INTERFACE = "interface";

   function className() { return self::INTERFACE; }
   
   function isImplem() { return false; }

}
