interface vy.geom.Filled @20240301 {

   import Sub=Shape;

   extend Shape;

   type {
      Filled = Shape.Shape;
      Sub = Sub.Shape;
      Brush;
   }

   function {
      createFilled( Sub, Brush ): Filled;
      shape( Filled ): Sub;
      brush( Filled ): Brush;
   }

}
