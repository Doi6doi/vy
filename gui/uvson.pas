unit uvson;

interface

uses
  Classes, SysUtils, ubstream;

type

   { TVson }

   EVson = class( Exception );

   TVsonKind = (vsNull, vsBool, vsInt, vsFloat, vsHandle, vsString,
      vsArray, vsMap);

   TVson = class
   protected
      FKind: TVsonKind;
      FScalar: Variant;
      procedure SetScalar( V: Variant );
      procedure ReadWS( S: TBStream );
      procedure ReadBool( S: TBStream );

   public
      procedure Read( S: TBStream );
      property Scalar: Variant read FScalar write SetScalar;
   end;

implementation

uses
   Variants;

procedure TVson.Read(S: TBStream);
begin
   ReadWS( S );
   case S.NextChar of
      't', 'f': ReadBool( S );
      else
         raise EVson.CreateFmt('Unknown char: ''%s''', [S.NextChar] );
   end;
end;

procedure TVson.SetScalar( V: Variant);
begin
   case VarType( V ) of
      varNull: begin
         FScalar := null;
         FKind := vsNull;
      end
      else
         raise EVson.CreateFmt('Unknown variant (%d): %s',
            [Ord(VarType(V)), VarToStr(V)] );
   end;
end;

procedure TVson.ReadWS( S: TBStream );
begin
   while (not S.Eos) and (S.NextChar in [#0,#9,#10,#13,' ']) do
      S.ReadChars(1);
end;

procedure TVson.ReadBool(S: TBStream);
begin
   if S.ReadIf( 'true' ) then
      Scalar := true
   else if S.ReadIf( 'false' ) then
      Scalar := false
   else
      raise EVson.CreateFmt('Unknown boolean: %s', [S.ReadChars(5)] );
end;

end.

