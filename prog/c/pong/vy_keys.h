#ifndef VY_KEYSH
#define VY_KEYSH

typedef VyType Key;

typedef struct Keys {
   Key (* byConst)( VyCStr cons );
   bool (* pressed)( Key key );} Keys;

VyImplemArgs keysArgs() {
   VyImplemArgs ret = vyImplemArgs("vy.ui.Keys", vyVer(20240301) );
   vyImplemArgsType( ret, "K", NULL );
   vyImplemArgsType( ret, "B", vyNative("bool") );
   vyImplemArgsFunc( ret, "byConst" );
   vyImplemArgsFunc( ret, "pressed" );
}


#endif // VY_KEYSH
