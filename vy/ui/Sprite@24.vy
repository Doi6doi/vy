interface vy.ui.Sprite @24 {

   extend View;

   import {
      vy.geom.Shape;
      vy.geom2.Transform;
   }

   method {
      Sprite( Shape );
      transform & Transform;
      shape & Shape;
   }   

}
