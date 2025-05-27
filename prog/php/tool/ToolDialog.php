<?php

namespace vy;

class ToolDialog extends PToolBase {

   function __construct() {
      parent::__construct();
      $this->addFuncs(["exec","menu"]);
   }

   function exec( $params ) {
      return (new Dialog( $params ))->exec();
   }
   
   /// menü készítése
   function menu( $title ) {
      return new PToolDialogMenu($title);
   }

}
