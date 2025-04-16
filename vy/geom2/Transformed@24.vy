interface vy.geom2.Transformed @24 {

   extend vy.geom.Shape;
 
   import {
      Sub = vy.geom.Shape;
      Transform;
   }

   method {
      Transformed( Sub );
      transform: & Transform;
      sub: & Sub;
   }

}
