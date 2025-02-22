interface vy.ui.Window @24 {

   extend Group;

   type {
      Window=Group.Group;
   }

   function {
      createWindow(): Window;
   }

}
