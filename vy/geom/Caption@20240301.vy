interface vy.geom.Caption @20240301 {

   import vy.char.String;

   extend Shape;

   type {
      Caption=Shape.Shape;
   }

   function {
      createCaption( String ): Caption;
   }

}
