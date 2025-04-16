interface vy.ui.Sprite @24 {

   extend View;

   import {
      vy.geom.Shape;
      vy.geom2.Vec;
   }

   function {
      create( Shape ): Sprite;
   }

   method {
      moveTo( Vec );
      setShape( Shape );
   }   

}
