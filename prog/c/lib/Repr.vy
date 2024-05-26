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
   Shape: refcount;
   Rect: inherit Shape;
   Circle: inherit Shape;
   Caption: inherit Shape;
   Filled: inherit Shape;
   ViewCoord: native VyViewCoord;
   View: refcount;
   Vector: refcount;
   Any: native VyAny;
   
}







