interface vy.core.Compare @20240117 {

   extend Equal;

   function {

      less(:,:):Bool { oper < }

      greater(:,:):Bool { oper > }

      lesseq(:,:):Bool { oper <= }

      greatereq(:,:):Bool { oper >= }

   }

   provide {
      given (a,b) {
         (a > b) = (b < a);
         (a <= b) = !(b < a);
         (a >= b) = !(a < b);
         if ( a < b ) a != b;
      }
   }

}

