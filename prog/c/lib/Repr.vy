representation {

   Bool: native bool;
   Unsigned: native unsigned;
   Index: native unsigned;
   Char: native wchar_t;
   String: refcount;
   Key: native VyKey;
   Float: native float;
   Stamp: native VyStamp;
   Color: native VyColor;
   Coord: native float;
   Font: refcount;
   Shape: public refcount;
   Transform: refcount;
   Square: inherit Shape;
   Circle: inherit Shape;
   Caption: inherit Shape {
      text: String;
   }
   Transformed: inherit Shape {
      transform: Transform;
      sub: Shape;
   }
   Filled: inherit Shape {
      color: Color;
      sub: Shape;
   }

   ViewCoord: native VyViewCoord;
   View: custom;
   Sprite: custom;
   Group: custom;
   Window: custom;

   Vector: refcount;
   Any: native VyAny;
   
}
