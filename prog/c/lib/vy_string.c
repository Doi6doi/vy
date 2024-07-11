#include <vy_implem.h>
#include "vy_string.h"
#include "vy_mem.h"
#include <string.h>

#define CHS sizeof(wchar_t)
#define MIN(a,b) ((a)<=(b)?(a):(b))


struct String {
   struct VyRefCount ref;
   struct VyMem mem;
};

VyRepr vyrString;

void destroyString( VyPtr p ) {
   vyMemResize( &((String)p)->mem, 0 );
}

static String vyStringConstAscii(VyCStr data, VySize len ) {
  if ( VY_LEN == len )
      len = strlen( data ) ;
   String ret = vyAlloc( vyrString );
   vyRefInit( (VyRefCount)ret );
   vyMemInit( & ret->mem, (len+1) * CHS );
   wchar_t * dest = (wchar_t *) ret->mem.data;
   for ( unsigned i=0; i<len; ++i )
      (*(dest++)) = (wchar_t)data[i];
   *dest = 0;
   return ret;
}

static String vyStringConstUtf(VyCStr s, VySize l ) {
   s = s; l = l;
   vyThrow("stub StringStringConstUtf");
}

static int vyStringCmp( String a, String b ) {
   VySize n = MIN( a->mem.size, b->mem.size ) / CHS;
   int ret = wcsncmp( vyStringPtr(a), vyStringPtr(b), n );
   if ( 0 == ret )
      return (int)(a->mem.size)-(int)(b->mem.size);
      else return ret;
}    

static bool vyStringLess(String a, String b) {
   return 0 > vyStringCmp( a, b );
}

static bool vyStringGreater(String a, String b) {
   return 0 < vyStringCmp( a, b );
}

static bool vyStringLesseq(String a, String b) {
   return 0 >= vyStringCmp( a, b );
}

static bool vyStringGreatereq(String a, String b) {
   return 0 <= vyStringCmp( a, b );
}

static bool vyStringEqual(String a, String b) {
   return 0 == vyStringCmp( a, b );
}

static bool vyStringNoteq(String a, String b) {
   return 0 != vyStringCmp( a, b );
}

static unsigned vyStringLength(String s) {
   return (unsigned)(s->mem.size / CHS);
}

static wchar_t vyStringCharAt(String s, unsigned i) {
   return vyStringPtr(s)[i];
}

wchar_t * vyStringPtr( String s ) {
   return (wchar_t *)s->mem.data;
}

void vyInitString( VyContext ctx ) {
   VYSTRINGARGS( ctx, args );
   vyrString = vyRepr( "String", sizeof(struct String), vySetRef, destroyString);
   vyArgsType( args, "String", vyrString );
   vyArgsImpl( args, "constAscii", (VyPtr)vyStringConstAscii );
   vyArgsImpl( args, "constUtf", (VyPtr)vyStringConstUtf );
   vyArgsImpl( args, "less", (VyPtr)vyStringLess );
   vyArgsImpl( args, "greater", (VyPtr)vyStringGreater );
   vyArgsImpl( args, "lesseq", (VyPtr)vyStringLesseq );
   vyArgsImpl( args, "greatereq", (VyPtr)vyStringGreatereq );
   vyArgsImpl( args, "equal", (VyPtr)vyStringEqual );
   vyArgsImpl( args, "noteq", (VyPtr)vyStringNoteq );
   vyArgsImpl( args, "length", (VyPtr)vyStringLength );
   vyArgsImpl( args, "charAt", (VyPtr)vyStringCharAt );
   vyAddImplem( ctx, args );
}

