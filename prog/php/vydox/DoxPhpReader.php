<?php

namespace vy;

/// dox PHP reader
class DoxPhpReader 
extends DoxReader
{
   
   function typ() { return DoxReader::PHP; }
 
   protected function refRexs() {
      return [];
   }
   
}
