interface vy.geom.Filled @24 {

   extend Shape;

   import Sub=Shape;

   type Brush;

   function {
      create( Sub, Brush ): Filled;
   }

   method {
      shape: Sub;
      brush: Brush;
   }

}
