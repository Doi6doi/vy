<?php

namespace vy;

class ToolDialog extends PToolBase {

   function __construct() {
      parent::__construct();
      $this->addFuncs(["exec"]);
   }

   function exec( $dlg ) {
      return Dialog::build( $dlg )->exec();
   }

}
