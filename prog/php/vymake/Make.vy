make {

   import { Deb; Dox; }

   init {
      init( "../../../MakeVals.vy" );

      $php := "php";
      $exe := "vymake";
      $ver := "20250222";

      $buildDir := "build";
      $linLibDir := "usr/lib/vy";
      $linBinDir := "usr/bin";
      $pkgs := ["lib","vymake","vydox"];

      $purge := [$buildDir, "*.deb"];
   }

   target {

      /// Create debian package
      deb {
         clean();
         mkdir( $buildDir );
         // copy files
         foreach ( d | $pkgs ) {
            bd := path( $buildDir, $linLibDir, d );
            mkdir( bd );
            copy( path("..",d,"*.php"), bd );
         }
         // create executable
         bbDir := path( $buildDir, $linBinDir );
         mkdir( bbDir );
         bin := format("%s /%s $@\n", 
            $php, path($linLibDir, "vymake","vymake.php"));
         bexe := path( bbDir,$exe );
         saveFile( bexe, bin );
         setPerm( bexe, "x" );
         // create description
         bdDir := path( $buildDir, "DEBIAN" );
         mkdir( bdDir );
         Dox.read("desc.dox");
         Dox.set("outType","txt");
         // create DEBIAN/control
         cnt := [
            "Package: "+$exe,
            "Version: "+$ver,
            "Architecture: "+Deb.arch( arch() ),
            "Maintainer: "+$author+" <"+$gitUrl+">",
            "Depends: php-cli",
            "Description: "+Dox.write()
         ];
         saveFile( path( bdDir, "control" ), implode("\n",cnt) );
         // build package
         Deb.build( $buildDir );
      }

      /// Purge generated files
      clean {
         purge( $purge );
      }

    }
}
