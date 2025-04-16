interface vy.geom2.Transformed @24 {

   extend vy.geom.Shape;
 
   import {
      Sub = vy.geom.Shape;
      Transform;
   }

   function {
      create( Sub ): Transformed;
   }

   method {
      transform: Transform;
      sub: Sub;
   }

}
