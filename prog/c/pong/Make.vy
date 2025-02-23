make {

   import { C; Debug; }

   target {

      run {
         build();
         libPath();
         exec( "./"+$prg );
      }

      build {
         init();
         genObj();
         genPrg();
      }

      clean {
         init();
         purge( [ $prg, $obj ] );
      }

      debug {
         build();
         libPath();
         Debug.debug( $prg );
      }

      help {
         echo([
            "Targets:",
            "   help: This help",
            "   build: Build program",
            "   run: Run built program",
            "   clean: Clean generated files",
            ""
         ]);
      }
   }

   function {
      init() {
         $src := "pong.c";
         $obj := "pong"+C.objExt();
         $prg := "pong"+exeExt();
//         C.set( "show", true);
         C.set( "debug", true);
         C.set( "warning", true);
         C.set( "incDir", ["../lib"]);
         C.set( "libDir", ["../lib","../sdl"]);
         C.set( "lib", ["m","vy","vysdl","SDL2"]);
      }

      /// object fordítása
      genObj() {
         if ( older( $obj, $src ))
            C.compile( $obj, $src );
      }
      
      /// könyvtár fordítása
      genPrg() {
         if ( older( $prg, $obj ))
            C.link( $prg, $obj );
      }

      /// lib könyvtár beállítása futtatáshoz
      libPath() {
         lp := getEnv("LD_LIBRARY_PATH");
         setEnv("LD_LIBRARY_PATH",lp+":../sdl:../lib");
      }

   } 

}

