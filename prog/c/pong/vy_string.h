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

#define VYSTRINGARGS( name ) \
   VyImplemArgs name = vyImplemArgs( "vy.char.String", vyVer(20240301)); \
   vyImplemArgsType( name, "Bool", vyNative("bool") ); \
   vyImplemArgsType( name, "Char", vyNative("wchar_t") ); \
   vyImplemArgsType( name, "String", NULL ); \
   vyImplemArgsFunc( name, "constAscii"); \
   vyImplemArgsFunc( name, "constUtf"); \
   vyImplemArgsFunc( name, "less"); \
   vyImplemArgsFunc( name, "greater"); \
   vyImplemArgsFunc( name, "lesseq"); \
   vyImplemArgsFunc( name, "greatereq"); \
   vyImplemArgsFunc( name, "equal"); \
   vyImplemArgsFunc( name, "noteq"); \

void vyInitString( VyContext );


#endif // VY_STRINGH
