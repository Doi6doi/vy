interface vy.geom2.Transformed @20240301 {

   extend vy.geom.Shape;
 
   import {
      Sub = vy.geom.Shape;
      Transform;
   }

   type {
      Transformed = Shape.Shape;
      Sub = Sub.Shape;
   }

   function {
      createTransformed( Sub ): Transformed;
      transform( Transformed ): Transform;
      sub( Transformed ): Sub;
   }

}
