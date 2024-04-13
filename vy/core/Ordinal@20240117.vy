interface vy.core.Ordinal @20240117 {

   extend Equal;

   type Ordinal = Equal.Equal;

   function {
      first:Ordinal;
      last:Ordinal;
      next(Ordinal):Ordinal;
      prev(Ordinal):Ordinal;
   }

   provide {
      given( o,u:Ordinal) {
         (u = next(o)) = (o = prev(u));
      }
   }

}
