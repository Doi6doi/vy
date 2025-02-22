interface vy.ui.Group @24 {

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
