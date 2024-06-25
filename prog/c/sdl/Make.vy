make {

   import { C; Comp; }

   target {

      build {
         init();
         genCodes();
         genRess();
         genDep();
         genObjs();
         genLib();
      }

      clean {
         init();
         purge( [ $lib, "*"+C.objExt() ] );
         foreach ( r | $ress )
            purge( codeFile(r[0]) );
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
      init() {
         $lib := C.libFile( "vysdl" );
         $items := ["caption","color","event","eventqueue","keyevent",
            "font","group","key","sprite","view","window"];
         $parts := ["vysdl"];
         $vyroot := "../../..";
         $vys := [
            [ "caption", "vy.geom2", "Caption", 20240301, "Caption=*" ],
            [ "color", "vy.geom", "Color", 20240301, "" ],
            [ "event", "vy.ui","Event", 20240301, "Event=*" ],
            [ "eventqueue", "vy.ui","EventQueue", 20240301, "" ],
            [ "font", "vy.geom2", "Font", 20240301, "Font=*" ],
            [ "group", "vy.ui", "Group", 20240301, "Group=*;Sub=View" ],
            [ "key", "vy.ui", "Key", 20240301, "" ],
            [ "keyevent", "vy.ui", "KeyEvent", 20240301, "KeyEvent=*" ],
            [ "sprite", "vy.ui", "Sprite", 20240301, "Sprite=*" ],
            [ "view", "vy.ui", "View", 20240301, "View=*" ],
            [ "window", "vy.ui", "Window", 20240301, "Window=*;Sub=View" ]
         ];
         $ress := [
            ["dvs_mini","dvs_mini.ttf"]
         ];
         $dep := "all.dep";
         Comp.setRepo( $vyroot );
         Comp.setReprs( ["../lib/Repr.vy", "Repr.vy"] );
//         C.setShow(true);
         C.setDebug(true);
         C.setLibMode(true);
         C.setWarning(true);
         C.setLib(["SDL2","SDL2_ttf"]);
         C.setIncDir([".","../lib"]);
      }

      /// generált .c fájlok készítése
      genCodes() {
         foreach ( i | $items )
            genCode( findVy( i ) );
      }
      
      /// erőforrás fájlok fordítása
      genRess() {
         foreach ( r | $ress )
            genRes( r );
      }

      /// depend fájl készítése
      genDep() {
         cs := [];
         hs := [];
         foreach ( i | $items ) {
            hs += libHeadFile( i );
            cs += codeFile( i );
         }
         foreach ( i | $parts ) {
            hs += headFile( i );
            cs += codeFile( i );
         }
         if ( older( $dep, cs+hs ) )
            C.depend( $dep, cs );
      }
      
      /// object fájlok fordítása
      genObjs() {
         deps := C.loadDep( $dep );
         foreach ( i | $parts + $items ) {
            of := objFile( i );
            if ( older( of, deps[of] ))
               C.compile( of, codeFile(i) );
         }
         foreach ( r | $ress ) {
            cf := codeFile( r[0] );
            of := objFile( r[0] );
            if ( older( of, cf ))
               C.compile( of, cf );
         }
      }
      
      /// könyvtár fordítása
      genLib() {
         objs := [];
         foreach ( i | $parts + $items )
            objs += objFile(i);
         foreach ( r | $ress )
            objs += objFile( r[0] );
         if ( older( $lib, objs ))
            C.link( $lib, objs );
      }

      /// .c fájl generálása
      genCode( v ) {
         df := codeFile( v[0] );
         sf := format( "%s/%s/%s@%s.vy", $vyroot, replace(v[1],".","/"),
            v[2], v[3] );
         if ( ! older( df, sf )) return;
         src := format("%s.%s@=%s", v[1], v[2], v[3]);
         Comp.setMap( v[4] );
         Comp.setForce( false );
         Comp.compile( src, df );
      }

      /// erőforrás fájl fordítása
      genRes( r ) {
         rf := codeFile( r[0] );
         if ( ! older( rf, r[1] )) return;
         C.sourceRes( rf, r[1], r[0] );
      }

      /// egy elemhez tartozó .h fájl
      headFile( i ) { return format("%s.h", i); }
      
      /// egy elemhez tartozó .h fájl a lib könyvtárban
      libHeadFile( i ) { return format("../vy_%s.h", i); }
      
      /// egy elemhez tartozó .c fájl
      codeFile( i ) { return format("%s.c", i ); } 

      /// egy elemhez tartozó obj fájl
      objFile( i ) { return format("%s%s", i, C.objExt()); }

      /// vy sor megkeresése
      findVy( x ) {
         for (i:=0; i < $vys.count ; ++i) {
            if ( $vys[i][0] = x )
               return $vys[i];
         }
         throw "Unkown item: "+x;
      }
   } 

}

