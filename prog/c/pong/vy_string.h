#ifndef VY_STRINGH
#define VY_STRINGH

typedef struct String * String;

typedef struct Strings {
   String (* constAscii)( size_t, VyCStr );
} Strings;

VyImplemArgs stringsArgs() {
   VyImplemArgs ret = vyImplemArgs("vy.char.String", vyVer(20240408) );
   vyImplemArgsType( ret, "Char", NULL );
   vyImplemArgsType( ret, "Index", NULL );
   vyImplemArgsType( ret, "String", NULL );
   vyImplemArgsFunc( ret, "constAscii" );
   return ret;
}

#endif // VY_STRINGH
