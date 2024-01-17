<?php

/// szerver interfész
class VIServ extends VIntf {

   static $ins;

   function name() { return "vsserv"; }

   function pkg() { return "phpvs"; }

   function version() { return "20230218"; }

}

VIServ::$ins = new VIServ();
