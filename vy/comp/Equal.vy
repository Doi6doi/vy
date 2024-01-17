interface vs.comp.EqualBase @20240117 {

   import vs.bool.Bool;

   type E;

   alias B = Bool.B;

   function {
      equal(E,E):B {
         infix = 40;
      }

      noteq(E,E):B {
         infix != 40;
      }
   }

   check {
      let a,b:E;
      if (a=b) b=a;
      (a!=b) = !(a=b);
   }




     
}
