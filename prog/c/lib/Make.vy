make {

   import C;

   target {

      build {
         init();
         generateHeads();
         generate( $lib );
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
         $vyroot := "../../..";
      }
      
      /// generált .h fájlok készítése
      generateHeads() {
         foreach ( h | $items + $hitems ) {
            hf := format( "vy_%s%s", h, ".h" );
            v := findVy( h );
            vf := format( "%s/%s/%s@%s.vy", $vyroot, replace(v[1],".","/"),
               v[2], v[3] );
            depend( hf, vf );
            rule( hf, vycHead );
            generate( hf );
         }
      }
      
      /// vy sor megkeresése
      findVy( x ) {
         for (i=0; i < $vys.count; ++i) {
            if ( $vys[i][0] == x )
               return $vys[i];
         }
      }
   } 

}

