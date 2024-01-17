<?php

/// vs függvény
abstract class VSFunc extends VSHandled {

   function handleKind() { return VSC::SERVERFUNC; }

   /// a függvény neve
   abstract function name();

}
