interface vy.geom.Circle @20240301 {

   extend Shape;

   type Circle = Shape.Shape;

   function {
      createCircle( radius: Coord ): Circle;
      radius( Circle ): Coord;
   }

}
