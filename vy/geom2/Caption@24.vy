interface vy.geom2.Caption @24 {

   extend vy.geom.Shape;

   import {
      vy.char.String;
      Font;
   }

   function {
      create( text: String, font: Font ): Caption;
   }

   method {
      text: String;
      font: Font;
   }

}
