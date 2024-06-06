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
   Shape: public refcount;
   Rect: inherit Shape {
      width: Coord;
      height: Coord;
   }
   Circle: inherit Shape {
      radius: Coord;
   }
   Caption: inherit Shape {
      text: String;
   }
   Filled: inherit Shape {
      color: Color;
   }

   ViewCoord: native VyViewCoord;
   View: custom;
   Sprite: custom;
   Group: custom;
   Window: custom;

   Vector: refcount;
   Any: native VyAny;
   
}
