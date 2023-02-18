<?php

/// vs interfész
abstract class VIntf extends VSHandled {

   /// interfész neve
   abstract function name();

   /// teljes név (csomag, név, verzió)
   function fullName() {
      $ret = $this->name();
      if ( $p = $this->pkg() )
         $ret = "$p:$ret";
      if ( $v = $this->version() )
         $ret = "$ret:$v";
      return $ret;
   }

   function handleKind() { return 2; }
}
