<?php

namespace vy;

/// dox C++ olvasó
class DoxCppReader 
extends DoxReader
{
   
   /// az olvasó típusa
   function typ() { return DoxReader::CPP; }
 
   protected function refRexs() {
      return [
         ':^#define\s+((\S+)\(.*?\)):' => DoxPart::MACRO,
         ':^#define\s+((\S+)):' => DoxPart::MACRO,
         '#^typedef\s*(.*?\(\s*\*\s*(\S+)\s*\)\(.*\))\s*;#' => DoxPart::FUNC,
         '#^typedef\s.*?((\S+))\s*;#' => DoxPart::TYPE,
         '#^(.*?\s+operator.*?)$#' => DoxPart::FUNC,
         '#^(.*?(\S+)\(.*\))\s*(?:;|{)#' => DoxPart::FUNC,
         '#(?:^|\s+)((?:struct|class)\s+(\S+).*?)\s*{#' => DoxPart::CLS,
         '#\S+\s+((\S+));$#' => DoxPart::FIELD,
      ];
   }
   
}
