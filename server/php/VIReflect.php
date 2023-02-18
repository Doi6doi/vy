<?php

/// reflection interfész
class VIReflect extends VIntf {

   static $ins;

   function name() { return "reflect"; }

   function version() { return "20230218"; }

   function pkg() { return "vs"; }

}

VIReflect::$ins = new VIReflect();
