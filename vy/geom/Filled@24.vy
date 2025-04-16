interface vy.geom.Filled @24 {

   extend Shape;

   import Sub=Shape;

   type Brush;

   method {
      Filled( Sub, Brush );
      shape: Sub;
      brush: Brush;
   }

}
