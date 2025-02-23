make {

   import { Deb; Dox; }

   init {
      init( "../../../MakeVals.vy" );

      $name := "vymake";
      $ver := "20250222";

      $buildDir := "build";
      $linLibDir := "usr/lib/vy";
      $linBinDir := "usr/bin";
      $pkgs := ["lib","vymake","vydox"];

      $exe := $name+exeExt();
      $purge := [$buildDir, "*.deb"];
   }

   target {

      /// deb or vypack
      default {
         if ( "Linux" = system() )
            deb();
            else vypack();
      }

      /// Create debian package
      deb {
         clean();
         makeDeb();
      }

      /// package with vypack
      vypack {
         makeVypack();
      }

      /// Purge generated files
      clean {
         purge( $purge );
      }

    }

   function {

      /// Create .deb package
      makeDeb() {
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
         ds := replace( Dox.write(), "\n", "\n " );
         // create DEBIAN/control
         cnt := [
            "Package: "+$exe,
            "Version: "+$ver,
            "Architecture: "+Deb.arch( arch() ),
            "Maintainer: "+$author+" <"+$gitUrl+">",
            "Depends: php-cli",
            "Description: "+ds
         ];
         saveFile( path( bdDir, "control" ), implode("\n",cnt) );
         // build package
         Deb.build( $buildDir );
      }

      /// pack with vypack
      makeVypack() {
         echo("Generating "+$exe);
         cmd := format( "%s -o %s -x '%s' -a vymake.php -r ../lib -r ../vydox -r ../vymake -v %s",
            $vypack, $exe, which($php), $ver );
echo( cmd );
         exec( cmd );
      }

   }

}
