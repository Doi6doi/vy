interface vy.char.String @20240408 {

   extend vy.core.Compare, vy.cont.Array;

   type Char = Array.Base;
   type String = Compare.C;

   const &ascii: String;
   const &utf: String;   
   
}
