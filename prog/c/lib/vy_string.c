#include "vy_string.h"
#include "vy_implem.h"
#include <wchar.h>

#define REALLOC(p,s) realloc( p, s)

VyRepr vyrString = NULL;

typedef struct VyString {
   VyRepr repr;
   size_t length;
   wchar_t * data;
} * VyString;


VyString vyStringConstAscii( size_t len, VyCStr data ) {
   wchar_t * d = REALLOC( NULL, len*sizeof(wchar_t) );
   if ( ! d )
      vyThrow( VYNOMEM );
   VyString ret = (VyString)vyAlloc( vyrString );
   ret->length = len;
   for (unsigned i=0; i<len; ++i)
      d[i] = (wchar_t)data[i];
   ret->data = d;
   return ret;
}

void vyDestroyString( VyString str ) {
   str->data = REALLOC( str->data, 0 );
}

void vyStringInit( VyContext ctx ) {
   vyrString = vyRepr( sizeof(VyPtr), true, (VyDestr)vyDestroyString );
   VyImplemArgs ia = vyImplemArgs( "vy.string.String", vyVer(20240408) );
   vyImplemArgsType( ia, "Char", vyNative( "wchar" ));
   vyImplemArgsType( ia, "Index", vyNative( "size" ));
   vyImplemArgsType( ia, "String", vyrString );
   vyImplemArgsImpl( ia, "constAscii", vyStringConstAscii );
   vyAddImplem( ctx, ia );
}
