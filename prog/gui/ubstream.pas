unit ubstream;

interface

uses
  Classes, SysUtils;

type

   { TBStream }

   TBStream = class( TStream )
   protected
      FBuffer: AnsiString;
      FBase: TStream;
      procedure SetBase( AValue: TStream );
      function Grow( N: Integer ): Boolean;
   public
      constructor Create( ABase: TStream );
      function Eos: Boolean;
      function NextChar: AnsiChar;
      function NextChars( N: Integer ): AnsiString;
      function ReadChar: AnsiChar;
      function ReadChars( N: Integer ): AnsiString;
      procedure WriteStr( S: AnsiString );
      procedure ReadStr( S: AnsiString );
      function ReadIf( S: AnsiString ): Boolean;
      property Base: TStream read FBase write SetBase;
   end;

implementation

uses
   Math;

const
   ReadStep = 512;

procedure TBStream.SetBase(AValue: TStream);
begin
   FBuffer := '';
   FBase := AValue;
end;

function TBStream.Grow( N: Integer ): Boolean;
var
   Part: array[0..ReadStep-1] of AnsiChar;
   S: String;
   M: Integer;
begin
   Part[0] := #0;
   Result := false;
   while Length( FBuffer ) < N do begin
      M := FBase.Read( Part, Min( N, ReadStep ) );
      if 0 = M then Exit;
      SetString( S, Part, N );
      FBuffer := FBuffer + S;
   end;
   Result := true;
end;

constructor TBStream.Create(ABase: TStream);
begin
   inherited Create;
   FBase := ABase;
   FBuffer := '';
end;

function TBStream.Eos: Boolean;
begin
   if '' = FBuffer
      then Result := not Grow(1)
      else Result := false;
end;

function TBStream.NextChar: AnsiChar;
begin
   if Eos
      then Result := #0
      else Result := FBuffer[1];
end;

function TBStream.NextChars(N: Integer): AnsiString;
begin
   Grow( N );
   Result := Copy( FBuffer, 1, N );
end;


function TBStream.ReadChars(N: Integer): AnsiString;
begin
   Result := NextChars(N);
   Delete( FBuffer, 1, Length(Result) );
end;

function TBStream.ReadChar: AnsiChar;
begin
   if Eos then
      raise EStreamError.Create( 'unexpected end of stream' );
   Result := FBuffer[1];
   Delete( FBuffer, 1, 1 );
end;

procedure TBStream.ReadStr(S: AnsiString);
var
   NS: AnsiString;
begin
   NS := ReadChars( Length(S) );
   if NS <> S then
      raise EStreamError.CreateFmt( '''%s'' expected but ''%s'' found', [S,NS] );
end;


procedure TBStream.WriteStr( S: AnsiString );
begin
   WriteBuffer( PAnsiChar(S)^, Length(S) );
end;


function TBStream.ReadIf(S: AnsiString): Boolean;
var
   N: Integer;
begin
   N := Length(S);
   Result := S = NextChars( N );
   if Result then ReadChars( N );
end;

end.

