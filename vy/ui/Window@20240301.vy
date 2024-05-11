interface vy.ui.Window @20240301 {

   extend Group;

   type {
      Window=Group.Group;
   }

   function {
      createWindow(): Window;
   }

}
