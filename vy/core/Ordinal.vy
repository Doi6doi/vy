interface vy.core.Ordinal @20240117 {

   extends Equal;

   type Ordinal = Equal.Equal;

   function {
      first:Ordinal;
      last:Ordinal;
      next(Ordinal):Ordinal;
      prev(Ordinal):Ordinal;
   }

   provide {
      given o,u:O;
      (u = next(o)) = (o = prev(u))
   }

}
