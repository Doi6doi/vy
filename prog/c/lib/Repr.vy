representation {

   Any: native VyAny;
   Bool: native bool;
   Caption: custom;
   Char: native wchar_t;
   Circle: inherit Shape;
   Color: native VyColor;
   Coord: native float;
   Event: custom;
   EventKind: native VyEventKind;
   Filled: inherit Shape {
      color: Color;
      sub: Shape;
   }
   Float: native float;
   Font: refcount;
   Group: custom;
   Index: native unsigned;
   Key: native VyKey;
   KeyEvent: custom;
   KeyEventKind: native VyKeyEventKind;
   Redraw: custom;
   Shape: public refcount;
   Sprite: custom;
   Stamp: native VyStamp;
   String: refcount;
   Square: inherit Shape;
   Transform: public refcount {
      sx: Coord;
      rx: Coord;
      mx: Coord;
      ry: Coord;
      sy: Coord;
      my: Coord;
   }
   Transformed: inherit Shape {
      transform: Transform;
      sub: Shape;
   }
   Unsigned: native unsigned;
   Vector: refcount;
   View: custom;
   ViewCoord: native VyViewCoord;
   Window: custom;
   
}
