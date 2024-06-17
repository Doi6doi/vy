interface vy.geom2.Transform @20240301 {

   import vy.num.Number;

   type Transform;

   const {
      ident: Transform;
      rotFull: Number;
   }

   function {
      createIdent: Transform;
      scale( & Transform, Number, Number );
      rotate( & Transform, Number );
      move( & Transform, Number, Number );
   }

}
