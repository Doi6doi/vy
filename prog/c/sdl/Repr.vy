representation {

   Caption: inherit Shape {
      text: String;
      font: Font;
   }
   Event: public refcount;
   KeyEvent: inherit Event;
   Font: refcount;
   View: public refcount {
      group: Group;
   }
   Sprite: inherit View {
      shape: Shape;
   }
   Group: inherit View {
      items: Vector;
   }
   Window: inherit Group;

}
