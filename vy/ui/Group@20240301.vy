interface vy.ui.Group @20240301 {

   import SubView=View;

   extend View;

   type {
      Group = View.View;
      Sub = SubView.View;
   }

   function {
      add( Group, Sub );
      remove( Group, Sub );
   }

}
