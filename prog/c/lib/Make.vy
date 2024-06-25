make {

   import { C; Comp; }

   target {

      build {
         init();
         genHeads();
         genCodes();
         genDep();
         genObjs();
         genLib();
      }

      clean {
         init();
         purge( [ $lib, "*"+C.objExt() ] );
         foreach ( h | $items + $hitems )
            purge( headFile(h) );
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
         $lib := C.libFile( "vy" );
         $libs := [];
         $vyh := "vy.h";
         $items := ["circle","filled",
            "random","square","transform","transformed",
            "shape","string","time","vector"];
         $hitems := ["caption","color","event","eventqueue",
            "font","group","key","keyevent", "sprite","view","window"];
         $parts := ["implem","cont","core","geom","geom2","mem","sm","ui",
            "util","vec"];
         case ( system() ) {
            "Windows": $parts += "windows";
            "Linux": $parts += "linux";
         }
         $vyroot := "../../..";
         $vys := [
            [ "caption", "vy.geom2", "Caption", 20240301, "Caption=*" ],
            [ "circle", "vy.geom2", "Circle", 20240301, "Circle=*" ],
            [ "color", "vy.geom", "Color", 20240301, "" ],
            [ "event", "vy.ui", "Event", 20240301, "" ],
            [ "eventqueue", "vy.ui", "EventQueue", 20240301, "" ],
            [ "font", "vy.geom2", "Font", 20240301, "Font=*" ],
            [ "filled", "vy.geom", "Filled", 20240301, "Filled=*;Sub=Shape;Brush=Color" ],
            [ "group", "vy.ui", "Group", 20240301, "Group=*;Sub=View" ],
            [ "key", "vy.ui", "Key", 20240301, "" ],
            [ "keyevent", "vy.ui", "KeyEvent", 20240301, "" ],
            [ "random", "vy.util", "Random", 20240301, "Number=Unsigned" ],
            [ "square", "vy.geom2", "Square", 20240301, "Square=*" ],
            [ "shape", "vy.geom", "Shape", 20240301, "Shape=*" ],
            [ "sprite", "vy.ui", "Sprite", 20240301, "Sprite=*" ],
            [ "string", "vy.char", "String", 20240301, "String=*" ],
            [ "time", "vy.util", "Time", 20240301, "Number=Float" ],
            [ "transform", "vy.geom2", "Transform", 20240301, "Transform=*;Number=Float" ],
            [ "transformed", "vy.geom2", "Transformed", 20240301, "Transformed=*;Number=Float;Sub=Shape" ],
            [ "vector", "vy.cont", "Vector", 20240301, "Vector=*;Value=Any" ],
            [ "view", "vy.ui", "View", 20240301, "View=*" ],
            [ "window", "vy.ui", "Window", 20240301, "Window=*;Sub=View" ]
         ];
         $dep := "all.dep";
         Comp.setRepo( $vyroot );
         Comp.setReprs( "Repr.vy" );
//         C.setShow(true);
         C.setWarning(true);
         C.setDebug( true );
         C.setLibMode( true );
         C.setIncDir(".");
      }

      /// generált .h fájlok készítése
      genHeads() {
         foreach ( i | $items + $hitems )
            genHead( findVy( i ) );
      }
      
      /// generált .c fájlok készítése
      genCodes() {
         foreach ( i | $items )
            genCode( findVy( i ) );
      }
      
      /// depend fájl készítése
      genDep() {
         cs := [];
         hs := [$vyh];
         foreach ( i | $items + $parts ) {
            cs += codeFile( i );
            hs += headFile( i );
         }
         foreach ( i | $hitems )
            hs += headFile( i );
         if ( older( $dep, cs+hs ) )
            C.depend( $dep, cs );
      }
      
      /// object fájlok fordítása
      genObjs() {
         deps := C.loadDep( $dep );
         foreach ( i | $items + $parts ) {
            of := objFile( i );
            if ( older( of, deps[of] ))
               C.compile( of, codeFile(i) );
         }
      }
      
      /// könyvtár fordítása
      genLib() {
         objs := [];
         foreach ( i | $items + $parts )
            objs += objFile(i);
         if ( older( $lib, objs ))
            C.link( $lib, objs, $libs );
      }

      /// .h féj generálása
      genHead( v ) {
         df := headFile( v[0] );
         sf := format( "%s/%s/%s@%s.vy", $vyroot, replace(v[1],".","/"),
            v[2], v[3] );
         if ( ! older( df, sf )) return;
         src := format("%s.%s@=%s", v[1], v[2], v[3]);
         Comp.setMap( v[4] );
         Comp.setForce( true );
         Comp.compile( src, df );
         case ( v[0] ) {
            "color": addHead( df, "#include <stdint.h>\n\ntypedef uint32_t VyColor;" );
            "event": addHead( df, "typedef enum VyEventKind {\n"
               +"   VE_NONE, VE_KEY, VE_MOUSE\n"
               +"} VyEventKind;" );
            "filled": addHead( df, "#include \"vy_color.h\"" );
            "key": addHead( df, "#include <stdint.h>\n\n"
               +"typedef uint32_t VyKey;" 
            );
            "keyevent": addHead( df, "#include \"vy_key.h\"\n"
               +"#include \"vy_event.h\"\n\n"
               +"typedef enum VyKeyEventKind {\n"
               +"   VKK_NONE, VKK_DOWN, VKK_UP, VKK_PRESS\n"
               +"} VyKeyEventKind;" 
            );
            "string": addTail( df, "wchar_t * vyStringPtr( String );" );
            "time": addHead( df, "typedef double VyStamp;" );
            "view": addHead( df, "typedef enum VyViewCoord {\n"
               +"   VC_NONE, VC_LEFT, VC_TOP, VC_WIDTH, VC_HEIGHT,\n"
               +"   VC_RIGHT, VC_BOTTOM, VC_CENTERX, VC_CENTERY\n"
               +"} VyViewCoord;"
            );
            "window": addHead( df, "#include \"vy_view.h\"" );
         }
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

      /// egy elemhez tartozó .h fájl
      headFile( i ) { return format("vy_%s.h", i); }
      
      /// egy elemhez tartozó .c fájl
      codeFile( i ) { return format("vy_%s.c", i ); } 
      
      /// egy elemhez tartozó obj fájl
      objFile( i ) { return format("vy_%s%s", i, C.objExt()); }

      /// vy sor megkeresése
      findVy( x ) {
         for (i:=0; i < $vys.count ; ++i) {
            if ( $vys[i][0] = x )
               return $vys[i];
         }
         throw "Unkown item: "+x;
      }
      
      /// fájlba beszúrás előre
      addHead( f, r ) {
         s := loadFile( f );
         s := replace( s, "#include <vy.h>", "#include <vy.h>\n\n"+r );
         saveFile( f, s );
      }

      /// fájlba beszúrás hátra
      addTail( f, r ) {
         s := loadFile( f );
         s := replace( s, "#endif", r+"\n\n#endif" );
         saveFile( f, s );
      }
      
   } 

}

