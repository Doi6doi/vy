make {

   import { C; Comp; }

   target {

      build {
         init();
         genHeads();
         genLib();
      }

      clean {
         init();
         purge( [ $lib, C.objFiles(), $heads ] );
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
         $lib := C.libFile("vy");
         $items := ["string","rect","random","color","circle",
            "caption","filled","shape"];
         $hitems := ["key","window","view","vector"];
         $parts := ["","core","geom","mem","sm","ui","util"];
         case ( system() ) {
            "Windows": $parts += "windows";
            "Linux": $parts += "linux";
         }
         $vys := [
            [ "string", "vy.char", "String", 20240301 ],
            [ "rect", "vy.geom", "Rect", 20240301 ],
            [ "random", "vy.util", "Random", 20240301 ],
            [ "color", "vy.geom", "Color", 20240301 ],
            [ "circle", "vy.geom", "Circle", 20240301 ],
            [ "caption", "vy.geom", "Caption", 20240301 ],
            [ "filled", "vy.geom", "Filled", 20240301 ],
            [ "shape", "vy.geom", "Shape", 20240301 ],
            [ "key", "vy.ui", "Key", 20240301 ],
            [ "window", "vy.ui", "Window", 20240301 ],
            [ "view", "vy.ui", "View", 20240301 ],
            [ "vector", "vy.cont", "Vector", 20240301 ]
         ];
         Comp.setRepo( "../../.." );
         Comp.setRepr( "Repr.vy" );
      }
      
      /// fejléc fordítása
      genHead( v ) {
         df := format( "vy_%s%s", h, ".h" );
         sf := format( "%s/%s/%s@%s.vy", $vyroot, replace(v[1],".","/"),
            v[2], v[3] );
         depend( df, sf );
         if ( ! needGen( df ) ) return;
         src = format("%s.%s@=%s", v[1], v[2], v[3]);
         Comp.setForce( true );
         Comp.compile( src, df );
      }
      
      /// generált .h fájlok készítése
      genHeads() {
         foreach ( h | $items + $hitems ) {
            if ( ! v := findVy( h ) )
               throw "Unkown part: "+h;
            genHead( v );
         }
      }
      
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

