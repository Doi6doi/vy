interface vy.cont.Indexable @24 {

   type {
      Index;
      Value;
   }

   method {
      at( Index ) & Value { oper [] };
   }

}
