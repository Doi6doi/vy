interface vy.geom2.Caption @24 {

   extend vy.geom.Shape;

   import {
      vy.char.String;
      Font;
   }

   method {
      Caption;
      text & String;
      font & Font;
   }

}
