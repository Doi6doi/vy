interface vy.ui.View @20240301 {

   import {
      vy.num.Number;
      ViewCoord;
   }

   type {
      View;
      Coord = Number.Number;
   }

   function {
      coord( View, ViewCoord ): Coord;
      setCoord( View, ViewCoord, Coord );
   }

}
