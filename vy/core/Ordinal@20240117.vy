interface vy.core.Ordinal @20240117 {

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
