make {

   import C;

   target {

      build {
         generate( libFile() );
      }

      clean {
         purge( [ libFile(), C.objFiles(), genHeads() ] );
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
      libFile() { return C.libFile("vy"); }
   } 

}

