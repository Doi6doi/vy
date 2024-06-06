make {

   import { C; }

   target {

      run {
         init();
         setEnv("LD_LIBRARY_PATH","../sdl:../lib");
         exec( "./"+$prg );
      }

      build {
         init();
         genObj();
         genPrg();
      }

      clean {
         init();
         purge( [ $prg ] );
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
         C.setShow(true);
         C.setIncDir(["../lib"]);
         C.setLibDir(["../lib","../sdl"]);
         C.setLib(["m","vy","vysdl"]);
      }

      /// object fordítása
      genObj() {
         if ( older( $obj, $src ))
            C.compile( $obj, $src );
      }
      
      /// könyvtár fordítása
      genPrg() {
         if ( older( $prg, $obj ))
            C.linkPrg( $prg, $obj );
      }

   } 

}

