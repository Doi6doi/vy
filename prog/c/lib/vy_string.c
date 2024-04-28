#include <vy_implem.h>
#include "vy_string.h"

static String vyStringConstAscii(VyCStr, VySize ) {
   vyThrow("stub StringConstAscii");
}

static String vyStringConstUtf(VyCStr, VySize ) {
   vyThrow("stub StringConstUtf");
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
   VYSTRINGARGS( args );
   vyImplemArgsImpl( args, "constAscii", &vyStringConstAscii );
   vyImplemArgsImpl( args, "constUtf", &vyStringConstUtf );
   vyImplemArgsImpl( args, "less", &vyStringLess );
   vyImplemArgsImpl( args, "greater", &vyStringGreater );
   vyImplemArgsImpl( args, "lesseq", &vyStringLesseq );
   vyImplemArgsImpl( args, "greatereq", &vyStringGreatereq );
   vyImplemArgsImpl( args, "equal", &vyStringEqual );
   vyImplemArgsImpl( args, "noteq", &vyStringNoteq );
   vyAddImplem( ctx, args );
}

