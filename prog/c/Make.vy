make {

   target {

      all {
         make( "lib", "build" );
         make( "sdl", "build" );
         make( "pong", "run" );
      }

      clean {
         make( "lib", "clean" );
         make( "sdl", "clean" );
         make( "pong", "clean" );
      }

      re {
         clean();
         all();
      }

      help {
         echo([
            "Targets:",
            "   all: Build all c programs",
            "   clean: Clean all subprojects",
            "   re: Clean subprojects and build all",
            ""
         ]);
      }
   }
   
}

