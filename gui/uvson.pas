unit uvson;

interface

uses
  Classes, SysUtils, ubstream;

type

   { TVson }

   EVson = class( Exception );

   TVsonKind = (vsNull, vsBool, vsInt, vsFloat, vsHandle, vsString,
      vsBinary, vsArray, vsDict);

   TVHandle = record
      Kind: LongWord;
      Value: LongWord;
   end;

   TVson = class
   protected
      FKind: TVsonKind;
      FScalar: Variant;
      FItems: TStringList;
      procedure SetScalar( V: Variant );
      procedure SetKind( K: TVsonKind );
      function GetHasItems: Boolean;
      procedure SetHasItems( H: Boolean );
      function GetItem( I: Integer ): TVson;
      function GetField( F: Utf8String ): TVson;
      function GetName( I: Integer ): Utf8String;
      function GetHandle: TVHandle;
      procedure SetHandle( Value: TVHandle );
      procedure ReadWS( S: TBStream );
      procedure ReadBool( S: TBStream );
      procedure ReadArray( S: TBStream );
      procedure ReadDict( S: TBStream );
      procedure ReadHandle( S: TBStream );
      function ReadString( S: TBStream ): Utf8String;
      function ReadEscape( S: TBStream ): WideChar;
      function ReadNat( S: TBStream ): LongWord;
      function ReadHex( S: TBStream; N: Integer ): LongWord;
      function HexValue( Ch: AnsiChar ): Integer;
      property HasItems: Boolean read GetHasItems write SetHasItems;
   public
      constructor Create;
      constructor CreateCopy( Other: TVson );
      procedure Assign( Other: TVson );
      procedure Read( S: TBStream );
      function Dump: String;
      function Count: Integer;
      procedure Check( AKind: TVsonKind );
   public
      property Handle: TVHandle read GetHandle write SetHandle;
      property Scalar: Variant read FScalar write SetScalar;
      property Kind: TVsonKind read FKind write SetKind;
      property Items[ I: Integer ]: TVson read GetItem;
      property Fields[ F: Utf8String ]: TVson read GetField; default;
      property Names[ I: Integer ]: Utf8String read GetName;
   public
      class function KindStr( Kind: TVsonKind ): Utf8String;
      class function CreateHandle( Kind, Value: Longword ): TVHandle;
   end;

implementation

uses
   Variants;

constructor TVson.Create;
begin
   FKind := vsNull;
   FScalar := Null;
   FItems := nil;
end;

constructor TVson.CreateCopy( Other: TVson );
begin
   Create;
   Assign( Other );
end;


procedure TVson.Assign( Other: TVson );
var
   i: Integer;
   F: Utf8String;
begin
   Kind := Other.Kind;
   if vsArray = Kind then begin
      for i := 0 to Other.Count-1 do
         Items[i].Assign( Other.Items[i] );
   end else if vsDict = Kind then begin
      for i := 0 to Other.Count-1 do begin
         F := Other.Names[i];
         Fields[ F ].Assign( Other.Items[i] );
      end;
   end;
end;

procedure TVson.Read(S: TBStream);
begin
   ReadWS( S );
   case S.NextChar of
      't', 'f': ReadBool( S );
      '[': ReadArray( S );
      '{': ReadDict( S );
      '$': ReadHandle( S );
      else
         raise EVson.CreateFmt('Unknown char: ''%s''', [S.NextChar] );
   end;
end;


function TVson.Dump: String;
begin
   case Kind of
      vsNull: Result := 'null';
      else
         raise EVson.CreateFmt('Unknown kind: %d', [Ord(Kind)] );
   end;
end;


function TVson.Count: Integer;
begin
   if HasItems
      then Result := FItems.Count
      else Result := 0;
end;


procedure TVson.Check( AKind: TVsonKind );
begin
   if AKind <> Kind then
      raise EVson.CreateFmt( '%s expected but %s found',
         [KindStr(AKind), KindStr(Kind)] );
end;

class function TVson.KindStr( Kind: TVsonKind ): Utf8String;
begin
   case Kind of
      vsNull: Result := 'null';
      vsBool: Result := 'bool';
      vsInt: Result := 'int';
      vsFloat: Result := 'float';
      vsString: Result := 'string';
      vsBinary: Result := 'binary';
      vsHandle: Result := 'handle';
      vsArray: Result := 'array';
      vsDict: Result := 'dict';
   end;
end;


class function TVson.CreateHandle( Kind, Value: Longword ): TVHandle;
begin
   Result.Kind := Kind;
   Result.Value := Value;
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

procedure TVson.SetKind( K: TVsonKind );
begin
   if Kind = K then Exit;
   HasItems := K in [vsArray, vsDict];
   if HasItems then
      FItems.Sorted := vsDict = K;
   case K of
      vsNull: FScalar := Null;
      vsBool: FScalar := false;
      vsInt: FScalar := 0;
      vsFloat: FScalar := 0.0;
      vsHandle: FScalar := Int64(0);
      vsString: FScalar := '';
   end;
   FKind := K;
end;


function TVson.GetHasItems: Boolean;
begin
   Result := Assigned( FItems );
end;


procedure TVson.SetHasItems( H: Boolean );
begin
   if H = HasItems then Exit;
   if H then begin
      FItems := TStringList.Create;
      FItems.OwnsObjects := true;
   end else begin
      FreeAndNil( FItems );
   end;
end;


function TVson.GetItem( I: Integer ): TVson;
begin
   Result := nil;
   if not HasItems then Exit;
   if I < 0 then Exit;
   if Count < I then Exit;
   if Count = I then
      FItems.AddObject( '', TVson.Create );
   Result := TVson( FItems.Objects[I] );
end;


function TVson.GetField( F: Utf8String ): TVson;
var
   i: Integer;
begin
   Result := nil;
   i := FItems.IndexOf( F );
   if 0 > i then
      i := FItems.AddObject( F, TVson.Create );
   Result := TVson( FItems.Objects[i] );
end;


function TVson.GetName( I: Integer ): Utf8String;
begin
   Result := '';
   if not HasItems then Exit;
   if I < 0 then Exit;
   if Count <= I then Exit;
   Result := Fitems[i];
end;


function TVson.GetHandle: TVHandle;
var
   I: Int64;
begin
   Result.Kind := 0;
   Result.Value := 0;
   if vsHandle = Kind then begin
      I := FScalar;
      Result.Kind := I shr 32;
      Result.Value := I and $ffffffff;
   end;
end;


procedure TVson.SetHandle( Value: TVHandle );
begin
   Kind := vsHandle;
   FScalar := Int64(Value.Kind) shl 32 or Value.Value;
end;

procedure TVson.ReadWS( S: TBStream );
begin
   while S.NextChar in [#0,#9,#10,#13,' '] do
      S.ReadChar;
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

procedure TVson.ReadArray(S: TBStream);
begin
   Kind := vsArray;
   S.ReadStr('[');
   ReadWS( S );
   while not S.ReadIf(']') do begin
      Items[ Count ].Read(S);
      ReadWS(S);
      S.ReadIf(',');
   end;
end;

procedure TVson.ReadDict( S: TBStream );
var
   F: UTF8String;
begin
   Kind := vsDict;
   S.ReadStr('{');
   ReadWS(S);
   while not S.ReadIf('}') do begin
      ReadWS(S);
      F := ReadString(S);
      ReadWS(S);
      S.ReadStr(':');
      Fields[F].Read(S);
      ReadWS(S);
      S.ReadIf(',');
   end;
end;


procedure TVson.ReadHandle( S: TBStream );
var
   I,J: LongWord;
begin
   S.ReadStr( '$' );
   I := ReadNat( S );
   S.ReadStr( ':' );
   J := ReadNat( S );
   FKind := vsHandle;
   FScalar := Int64( I ) shl 32 or J;
end;


function TVson.ReadString( S: TBStream ): Utf8String;
var
   Ret: WideString;
   Ch: AnsiChar;
begin
   Ret := '';
   S.ReadStr('"');
   while not S.Eos do begin
      Ch := S.ReadChar;
      if '"' = Ch then begin
         Result := UTF8Encode(Ret);
         Exit;
      end else if '\' = Ch then
         Ret := Ret + ReadEscape(S)
      else
         Ret := Ret + WideChar(Ch);
   end;
   raise EVson.Create('Unterminated string');
end;


function TVson.ReadEscape( S: TBStream ): WideChar;
var
   Ch: AnsiChar;
begin
   Ch := S.ReadChar;
   case Ch of
      '"', '\', '/': ;
      'b': Ch := #8;
      'f': Ch := #12;
      'n': Ch := #10;
      'r': Ch := #13;
      't': Ch := #9;
      'u': begin
         Result := WideChar( ReadHex(S,4) );
         Exit;
      end;
      else
         raise EVson.CreateFmt('unknown escape character: ''%s''', [Ch] );
   end;
   Result := WideChar(Ch);
end;


function TVson.ReadNat( S: TBStream ): LongWord;
var
   Ch: AnsiChar;
begin
   Result := 0;
   while not S.Eos do begin
      Ch := S.NextChar;
      if Ch in ['0'..'9'] then begin
         Result := 10*Result + Ord(Ch)-Ord('0');
         S.ReadChars(1);
      end else
         Exit;
   end;
end;


function TVson.ReadHex( S: TBStream; N: Integer ): LongWord;
var
   i: Integer;
begin
   Result := 0;
   for i := 1 to N do
      Result := 16*Result + HexValue( S.ReadChar );
end;


function TVson.HexValue( Ch: AnsiChar ): Integer;
begin
   case Ch of
      '0'..'9': Result := Ord(Ch)-Ord('0');
      'a'..'f': Result := Ord(Ch)-Ord('a')+10;
      'A'..'F': Result := Ord(Ch)-Ord('A')+10;
      else
         raise EVson.CreateFmt('unknown hex character: ''%s''', [Ch] );
   end;
end;

end.

