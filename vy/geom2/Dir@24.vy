interface vy.geom2.Dir {

   extends vy.core.Append;

   method {
      Dir;
      opposite: ;
   }

   const {
      none;
      left;
      right;
      up;
      down;
   }

   provide {
      left.opposite = right;
      up.opposite = down;
      none.opposite = none;
      given (a) {
         a.opposite.opposite = a;
      }
   }

}
