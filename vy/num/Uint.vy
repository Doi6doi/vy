interface vs.num.Uint @20240117 {

   extends 
      Number, vs.comp.Compare, vs.comp.Ordinal;

   type U = Compare.C = Number.N;

   constdecimal;

   check {
      let a:U;
      0 <= a;
   }

}

