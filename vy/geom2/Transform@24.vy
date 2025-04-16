interface vy.geom2.Transform @24 {

   extend vy.core.Assign;

   import vy.num.Number;

   const {
      ident: Transform;
      rotFull: Number;
   }

   function {
      ident: Transform;
   }

   method {
      scale( Number, Number ) & ;
      rotate( Number ) & ;
      move( Number, Number ) & ;
   }

}
