interface vy.geom2.Caption @20240301 {

   import {
      vy.char.String;
      Font;
   }

   extend vy.geom.Shape;

   type {
      Caption=Shape.Shape;
   }

   function {
      createCaption( text: String, font: Font ): Caption;
      text( Caption ): String;
      font( Caption ): Font;
   }

}
