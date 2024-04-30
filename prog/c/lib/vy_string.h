#ifndef VY_STRINGH
#define VY_STRINGH
#include <vy.h>

typedef struct String * String;

typedef struct StringFun {
   String (* constAscii)(VyCStr, VySize);
   String (* constUtf)(VyCStr, VySize);
   bool (* less)(String, String);
   bool (* greater)(String, String);
   bool (* lesseq)(String, String);
   bool (* greatereq)(String, String);
   bool (* equal)(String, String);
   bool (* noteq)(String, String);
} StringFun;

#define VYSTRINGARGS( ctx, name ) \
   VyArgs name = vyArgs( "vy.char.String", vyVer(20240301)); \
   vyArgsType( name, "Bool", vyNative( ctx, "bool" ) ); \
   vyArgsType( name, "Char", vyNative( ctx, "wchar_t" ) ); \
   vyArgsType( name, "String", NULL ); \
   vyArgsFunc( name, "constAscii"); \
   vyArgsFunc( name, "constUtf"); \
   vyArgsFunc( name, "less"); \
   vyArgsFunc( name, "greater"); \
   vyArgsFunc( name, "lesseq"); \
   vyArgsFunc( name, "greatereq"); \
   vyArgsFunc( name, "equal"); \
   vyArgsFunc( name, "noteq"); \

void vyInitString( VyContext );


#endif // VY_STRINGH
