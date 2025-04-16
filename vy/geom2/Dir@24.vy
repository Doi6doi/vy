interface vy.geom2.Dir {

   extends vy.core.Append;

   method {
      Dir;
      opposite: Dir;
   }

   const {
      none: Dir;
      left: Dir;
      right: Dir;
      up: Dir;
      down: Dir;
   }

   provide {
      left.opposite = right;
      up.opposite = down;
      none.opposite = none;
      given (a:Dir) {
         a.opposite.opposite = a;
      }
   }

}
