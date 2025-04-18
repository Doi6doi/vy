interface vy.core.Ordinal @24 {

   extend Equal;

   const {
      first;
      last;
   }

   method {
      next: ;
      prev: ;
   }

   provide {
      given (o,u) {
         (u = o.next) = (o = u.prev);
      }
   }

}
