interface vy.geom.Shape @20240301 {

   import {
      vy.num.Number;
      Color;
   }

   type {
      Shape;
      Coord = Number.Number;
   }

   function {
      setColor( Shape, Color );
   }

}
