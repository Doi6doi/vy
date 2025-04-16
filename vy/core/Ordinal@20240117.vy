interface vy.core.Ordinal @20240117 {

   extend Equal;

   function {
      first:Ordinal;
      last:Ordinal;
   }

   method {
      next:Ordinal;
      prev:Ordinal;
   }

   provide {
      given( o,u:Ordinal) {
         (u = o.next) = (o = u.prev);
      }
   }

}
