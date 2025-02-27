make {

   import { Deb; Dox; Net; Arc; }

   init {
      init( "../MakeVals.vy" );

      $name := "vymake";
      $ver := "20250222";

      $buildDir := "build";
      $pkgs := ["lib","vymake","vydox"];

      $exe := $name+exeExt();
      $purge := [$buildDir, "*.deb", "vymake"+exeExt() ];
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
         echo("Generating .deb package");
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

      // create vypack exe
      vypack() {
         echo("Generating vypack-ed executable");
         if ( ! which( $vypack ))
            echo("Cannot find "+$vypack);
         mkdir( $buildDir );
         // copy files
         foreach ( d | $pkgs ) {
            mkdir( path( $buildDir, d ));
            copy( path("..",d,"*.php"), path($buildDir,d) );
         }
         /// download and extract php zip
         bpf := path( $buildDir, $vypPhpFname );
         if ( ! exists( bpf )) {
            Net.set("cert",false);
            echo("Downloading php release");
            Net.fetch( $vypPhpUrl, bpf );
         }
         bpd := path( $buildDir, $vypPhpDir );
         Arc.set( "same", false );
         Arc.extract( bpf, bpd );
         foreach ( d | ["sasl2",path("lib","enchant")] )
            purge( path( bpd, d ));
         /// create vymake package
         cmd := format( "%s -o %s -c \"%s\" -a %s -r %s %s -v %s",
            $vypack, $exe,
            path("%vypack%",$vypPhpDir,"php.exe"),
            path("%vypack%","vymake","vymake.php"), 
            bpd, rArgs( $pkgs ), 
            $ver );
         echo( cmd );
         exec( cmd );
      }

      /// -r kapcsolók vymake-hez
      rArgs( dirs ) {
         return implode(" ",regexp(dirs, "#^(.*)$#", 
            "-r "+path($buildDir,"\\\\1")));
      }

   }

}
