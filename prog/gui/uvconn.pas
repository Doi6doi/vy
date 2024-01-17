unit uvconn;

interface

uses
  Classes, SysUtils, ubstream, uvs, uvson;

type
   TVCommand = class;

   TVConn = class
   protected
      FLocal, FRemote: TVSys;
      FBS: TBStream;
      procedure SetStream( AValue: TStream );
      procedure SetOnIntf( Value: TVIntfEvent );
      function CreateObj( A: TVSon ): TVObj;
   public
      constructor Create( ALocal: TVSys );
      destructor Destroy; override;
      /// egy parancs váltás
      function Send( Cmd: TVCommand ): TVCommand;
      /// teljes hívás
      function Call( Cmd: TVCommand ): TVCommand;
      /// kezdeti interfészek beolvasása
      procedure ReadHelo;
      /// távoli objektumok aktualizálása
      procedure UpdateObjs;
      /// a csatorna
      property Stream: TStream write SetStream;
      property Local: TVSys read FLocal;
      property Remote: TVSys read FRemote;
      property OnAddIntf: TVIntfEvent write SetOnIntf;
   end;

   TVCommand = class
   protected
      /// dump kitöltése adatokkal
      procedure FillDump( V: TVson ); virtual; abstract;
   public
      /// execute and dispose command
      procedure Process( Conn: TVConn ); virtual;
      /// dump vson data
      function Dump: AnsiString; virtual;
   end;

implementation

uses
   uvcommands;

procedure TVConn.SetStream(AValue: TStream);
begin
   FBS.Base := AValue;
end;


procedure TVConn.SetOnIntf( Value: TVIntfEvent );
begin
   FLocal.OnIntf := Value;
   FRemote.OnIntf := Value;
end;


function TVConn.CreateObj( A: TVSon ): TVObj;
begin
   A.Check( vsHandle );
   Result := TVObj.CreateHandle( A.Handle );
end;

constructor TVConn.Create;
begin
   FLocal := ALocal;
   FRemote := TVSys.Create;
   FBS := TBStream.Create(nil);
end;

destructor TVConn.Destroy;
begin
   FBS.Free;
   FRemote.Free;
end;

procedure TVConn.ReadHelo;
var
   Cmd: TVCommand;
begin
   Cmd := Send( nil );
   if not (Cmd is TVHelo) then
      raise EVS.CreateFmt( 'helo expected but %s received', [Cmd.ClassName] );
   Cmd.Process( Self );
end;


procedure TVConn.UpdateObjs;
var
   C: TVCall;
   R: TVResult;
   V: TVson;
   i: Integer;
   VO: TVObj;
begin
   C := nil;
   R := nil;
   try
      C := TVCall.CreateWith( Remote.Objects, nil );
      R := Call( C ) as TVResult;
      V := R.Value;
      Remote.ClearObjs;
      for i := 0 to V.Count-1 do begin
         VO := CreateObj( V.Items[i] );
         FRemote.AddObj( VO );
      end;
   finally
      R.Free;
      C.Free;
   end;
end;


function TVConn.Call( Cmd: TVCommand ): TVCommand;
var
   CC: TVCall;
   F: TVFunc;
   C, R: TVCommand;
begin
   if not (Cmd is TVCall) then
      raise EVS.Create('only TVCall can be called');
   CC := TVCall( Cmd );
   F := CC.Func;
   if F.Local then begin
      Result := TVResult.CreateWith( F.Execute( CC.Args ) );
   end else begin
      R := Send( CC );
      while true do begin
         if R is TVCall then begin
            C := R;
            R := Call( C );
            C.Free;
            C := R;
            R := Send( C );
            C.Free;
         end else if R is TVResult then begin
            Result := R;
            Exit;
         end;
      end;
   end;
end;


function TVConn.Send( Cmd: TVCommand ): TVCommand;
var
   A,V: TVson;
   H: TVHandle;
begin
   A := TVson.Create;
   try
      if Assigned( Cmd ) then
         FBS.WriteStr( Cmd.Dump );
      A.Read( FBS );
      if A.Kind <> vsArray then
         raise EVS.CreateFmt('unknown data: %s', [A.Dump] );
      V := A.Items[0];
      if V.Kind <> vsHandle then
         raise EVS.CreateFmt('unknown command: %s', [V.Dump] );
      H := V.Handle;
      if H.Kind <> VC.Command then
         raise EVS.CreateFmt('wrong command kind: %d', [H.Kind] );
      if VC.Helo = H.Value then
         Result := TVHelo.CreateVson( A )
      else
         raise EVS.CreateFmt('unknown command code: %d', [H.Value] );
   finally
      A.Free;
   end;
end;

procedure TVCommand.Process( Conn: TVConn );
begin
   Free;
end;


function TVCommand.Dump: AnsiString;
var
   V: TVson;
begin
   V := TVson.Create;
   try
      V.Kind := vsArray;
      FillDump( V );
      Result := V.Dump;
   finally
      V.Free;
   end;
end;

end.

