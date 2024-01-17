interface vy.num.Int32 @20240117 {

   extend BinInt;

   provide {
      next(2147483647) = -2147483648;
   }

}

