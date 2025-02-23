make {

   import { Deb; Dox; }

   init {
      init( "../../../MakeVals.vy" );
      $buildDir := "build";
      $linLibDir := "usr/lib/vy";
      $linBinDir := "usr/bin";
      $php := "php";
      $exe := "vymake";
      $ver := "20250222";
   }

   target {

      /// Create debian package
      deb {
         clear();
         // copy files
         bbDir := path( $buildDir, $linBinDir );
         bdDir := path( $buildDir, "DEBIAN" );
         bllDir := path( $buildDir, $linLibDir, "lib" );
         blmDir := path( $buildDir, $linLibDir, "vymake" );
         mkdir( [bbDir,bdDir,bllDir,blmDir] );
         copy( path("..","lib","*.php"), bllDir );
         copy( "*.php", blmDir );
         // create executable
         bin := format("%s /%s $@", $php, path($linBinDir,"vymake.php"));
         saveFile( path( bbDir, $exe ), bin );
         // create description
         Dox.read("desc.dox");
         Dox.set("outType","txt");
         // create DEBIAN/control
         cnt := [
            "Package: "+$exe,
            "Version: "+$ver,
            "Architecture: "+arch(),
            "Maintainer: "+$author+" <"+$gitUrl+">",
            "Depends: php (>=5)",
            "Description: "+Dox.write()
         ];
         saveFile( path( bdDir, "control" ), implode("\n",cnt) );
         // build package
         Deb.build( $buildDir );
      }

      /// Clear build directory
      clear {
         mkdir( $buildDir );
         purge( path($buildDir,"*") );
      }

    }
}
