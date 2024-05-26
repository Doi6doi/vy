interface vy.cont.Array @20240301 {

   extend Indexable;

   import vy.num.Uint;

   type {
      Array = Indexable.Indexable;
      Index = Uint.Uint;
   }

   function {
      length( Array ): Index;
      setValue( Array, Index, Value );
   }

}
