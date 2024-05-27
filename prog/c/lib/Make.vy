make {

   import C;

   target {

      build {
         init(true);
         generate( $lib );
      }

      clean {
         init();
         purge( [ $lib, C.objFiles(), $heads ] );
      }

      help {
         echo([
            "Targets:",
            "   help: This help",
            "   build: Build library",
            "   clean: Clean generated files",
            ""
         ]);
      }
   }

   function {
      init( dep ) {
         $lib := C.libFile("vy");
         $items := ["string","rect","random","color","circle",
            "caption","filled","shape"];
         $hitems := ["key","window","view","vector"];
      }
   } 

}

