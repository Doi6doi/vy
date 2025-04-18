interface vy.geom2.Dir @24 {

   extend vy.core.Equal;

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
