interface vy.ui.Group @24 {

   extend View;

   import {
      Sub = View;
   }

   method {
      add( Sub );
      remove( Sub );
   }

}
