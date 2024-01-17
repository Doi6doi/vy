interface vy.comp.Ordinal @20240117 {

   extends Equal;

   type O = Equal.E;

   function {
      first:O;
      last:O;
      next(O):O;
      prev(O):O;
   }

   check {
      let o,u:O;
      (u = next(o)) = (o = prev(u))
   }

}
