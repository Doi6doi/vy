<?php

namespace vy;

class Deb 
   extends CmdTool
{

   function __construct() {
      $this->show = true;
   }

   function executable() {
      return "dpkg-deb";
   }

}
