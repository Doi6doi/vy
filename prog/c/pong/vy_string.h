#ifndef VY_STRINGH
#define VY_STRINGH

typedef struct String * String;

typedef struct Strings {
   String (* create)();
} Strings;

VyImplemArgs stringsArgs() {
   VyImplemArgs ret = vyImplemArgs("vy.string.String", vyVer(20240327) );
   vyImplemArgsType( ret, "String", NULL );
   vyImplemArgsFunc( ret, "create" );
   return ret;
}

#endif // VY_STRINGH
