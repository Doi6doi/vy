interface vy.ui.View @24 {

   import {
      vy.num.Number;
      vy.geom2.Coord;
   }

   method {
      coord( Coord ): Number;
      setCoord( Coord, Number );
   }

}
