make {

   init {
      init( "../MakeVals.vy" );

      $name := "vymake";
      $ver := "20250228";

      $Net := tool("Net");

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

      dl {
         $Net.set("cert",false);
         echo( $Net.fetch("https://google.com") );
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
         Deb := tool("Deb");
         Dox := tool("Dox");
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
         Arc := tool("Arc");
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
            $Net.set("cert",false);
            echo("Downloading php release");
            $Net.fetch( $vypPhpUrl, bpf );
         }
         bpd := path( $buildDir, $vypPhpDir );
         Arc.set( "same", false );
         Arc.extract( bpf, bpd );
         $drops := ["sasl2","lib","dev","libsqlite3.dll",
            "php7embed.lib","php7phpdbg.dll","phpdbg.exe",
            "php-cgi.exe","php-win.exe",
            "icudt66.dll","icuin66.dll","icuuc66.dll"
            ];
         $dropExts := ["fileinfo","gd2","imap","ldap","snmp","soap",
            "tidy","xsl"];
         $lrgs := [
            "libcrypto-1_1-x64.dll","php7.dll",
            "ext\\php_mbstring.dll"];
         foreach ( d | $drops )
            purge( path( bpd, d ) );
         foreach ( d | $dropExts )
            purge( format("%s\\ext\\php_%s.dll", bpd, d ));
         /// upx large files
         if ( $upx ) {
            foreach ( l | $lrgs ) {
               echo("UPX-ing "+l);
               exec( $upx+" --force -q "+path( bpd, l ));
            }
         }
         /// add php.ini
         echo( "Adding php.ini");
         phi := ["extension=curl","extension_dir=.\\ext"];
         saveFile( bpd+"\\php.ini", implode("\n",phi));
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
