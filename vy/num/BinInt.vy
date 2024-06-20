interface vy.num.BinInt @20240117 {

   extend {
      extend vy.bin.Bits;
      extend Int;
   }

   type I = Int.I = Bits.D;

   const &hex: BinInt;

}

