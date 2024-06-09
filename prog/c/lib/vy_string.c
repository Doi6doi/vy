#include <vy_implem.h>
#include "vy_string.h"
#include "vy_mem.h"
#include <string.h>

#define CHS sizeof(wchar_t)

struct String {
   struct VyRefCount ref;
   struct VyMem mem;
};

VyRepr vyrString;

void destroyString( VyPtr ) {
   vyThrow("stub destroyString");
}

static String vyStringConstAscii(VyCStr data, VySize len ) {
  if ( VY_LEN == len )
      len = strlen( data ) ;
   String ret = vyAlloc( vyrString );
   vyRefInit( (VyRefCount)ret );
   vyMemInit( & ret->mem, len * CHS );
   wchar_t * dest = (wchar_t *) ret->mem.data;
   for ( unsigned i=0; i<len; ++i )
      (*(dest++)) = (wchar_t)data[i];
   return ret;
}

static String vyStringConstUtf(VyCStr, VySize ) {
   vyThrow("stub StringStringConstUtf");
}

static bool vyStringLess(String, String ) {
   vyThrow("stub StringLess");
}

static bool vyStringGreater(String, String ) {
   vyThrow("stub StringGreater");
}

static bool vyStringLesseq(String, String ) {
   vyThrow("stub StringLesseq");
}

static bool vyStringGreatereq(String, String ) {
   vyThrow("stub StringGreatereq");
}

static bool vyStringEqual(String, String ) {
   vyThrow("stub StringEqual");
}

static bool vyStringNoteq(String, String ) {
   vyThrow("stub StringNoteq");
}

void vyInitString( VyContext ctx ) {
   VYSTRINGARGS( ctx, args );
   vyrString = vyRepr( sizeof(struct String), false, destroyString);
   vyArgsType( args, "String", vyrString );
   vyArgsImpl( args, "constAscii", vyStringConstAscii );
   vyArgsImpl( args, "constUtf", vyStringConstUtf );
   vyArgsImpl( args, "less", vyStringLess );
   vyArgsImpl( args, "greater", vyStringGreater );
   vyArgsImpl( args, "lesseq", vyStringLesseq );
   vyArgsImpl( args, "greatereq", vyStringGreatereq );
   vyArgsImpl( args, "equal", vyStringEqual );
   vyArgsImpl( args, "noteq", vyStringNoteq );
   vyAddImplem( ctx, args );
}

