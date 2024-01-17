interface vs.num.Uint16 @20240117 {

   extends BinUnit;

   check {
      next(65535) = 0;
   }

}

