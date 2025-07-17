<?php

namespace vy;

/// dox C olvasó
class DoxCReader
   extends DoxReader
{

   /// az olvasó típusa
   function typ() { return DoxReader::C; }

   protected function refRexs() {
      return [
         ':^#define\s+((\S+)\(.*?\)):' => DoxPart::MACRO,
         ':^#define\s+((\S+)):' => DoxPart::MACRO,
         '#^typedef\s*(.*?\(\s*\*\s*(\S+)\s*\)\(.*\))\s*;#' => DoxPart::FUNC,
         '#^typedef\s.*?((\S+))\s*;#' => DoxPart::TYPE,
         '#^(.*?(\S+)\(.*\))\s*(?:;|{)#' => DoxPart::FUNC,
         '#(?:^|\s+)(struct\s+(\S+))\s*{#' => DoxPart::RECORD,
         '#\S+\s+((\S+));$#' => DoxPart::FIELD,
         '#^(?:typedef\s+)?(enum\s+(\S+))\s+{#' => DoxPart::ENUM,
         '#^([a-zA-Z_0-9]+).*(?:,|$)#' => DoxPart::ENUMITEM
      ];
   }

}
