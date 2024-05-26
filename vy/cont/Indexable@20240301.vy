interface vy.cont.Indexable @20240410 {

   type {
      Indexable;
      Index;
      Value;
   }

   function {
      value( Indexable, Index ): Value;
   }

}
