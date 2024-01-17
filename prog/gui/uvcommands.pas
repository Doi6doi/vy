unit uvcommands;

interface

uses
   Classes, SysUtils, uvs, uvson, uvconn;

type
   TVHelo = class( TVCommand )
   protected
      FIntfs: TVson;
      procedure FillDump( V: TVson ); override;
   public
      constructor CreateVson( V: TVson );
      destructor Destroy; override;
      procedure Process( Conn: TVConn ); override;
   end;

   TVResult = class( TVCommand )
   protected
      FValue: TVson;
      procedure FillDump( V: TVson ); override;
   public
      constructor Create;
      constructor CreateWith( AValue: TVson );
      destructor Destroy; override;
      /// eredmény típus ellenőrzés
      function Check( Kind: TVsonKind ): TVson;
   public
      property Value: TVson read FValue;
   end;

   TVCall = class( TVCommand )
   protected
      FFunc: TVFunc;
      FArgs: TVson;
      procedure FillDump( V: TVson ); override;
   public
      constructor CreateWith( Func: TVFunc; Args: TVson );
      destructor Destroy; override;
      property Func: TVFunc read FFunc;
      property Args: TVson read FArgs;
   end;

   TVCallName = class( TVCommand )
   protected
      FIntf: TVIntf;
      FName: Utf8String;
      procedure FillDump( V: TVson ); override;
   public
      constructor CreateWith( Intf: TVIntf; Name: Utf8String; Args: TVson );
      destructor Destroy; override;
      property Intf: TVIntf read FIntf;
      property Name: Utf8String read FName;
      property Args: TVson read FArgs;
   end;


implementation

constructor TVHelo.CreateVson( V: TVson );
begin
   if 2 > V.Count then
      raise EVS.Create('Interfaces missing');
   FIntfs := TVson.Create;
   FIntfs.Assign( V.Items[1] );
end;


destructor TVHelo.Destroy;
begin
   FreeAndNil( FIntfs );
   inherited Destroy;
end;

procedure TVHelo.Process( Conn: TVConn );
var
   I: Integer;
   VI: TVIntf;
begin
   for I := 0 to FIntfs.Count-1 do begin
      VI := Conn.Remote.AddIntf;
      VI.FullName := FIntfs.Names[i];
      VI.Handle := FIntfs.Items[i].Handle;
   end;
   inherited;
end;


procedure TVHelo.FillDump( V: TVson );
begin
   raise EVS.Create('cannot dump helo');
end;

constructor TVResult.Create;
begin
   inherited Create;
   FValue := TVson.Create;
end;

constructor TVResult.CreateWith( AValue: TVson );
begin
   Create;
   FValue.Assign( AValue );
end;


destructor TVResult.Destroy;
begin
   FValue.Free;
   inherited Destroy;
end;

function TVResult.Check( Kind: TVsonKind ): TVson;
begin
   FValue.Check( Kind );
   Result := FValue;
end;


procedure TVResult.FillDump( V: TVson );
begin
   V.Items[0].Handle := TVson.CreateHandle( VC.Command, VC.Result );
   V.Items[1].Assign( FValue );
end;

constructor TVCall.CreateWith( Func: TVFunc; Args: TVson );
begin
   inherited Create;
   FFunc := Func;
   FArgs := TVson.Create;
   if Assigned( Args ) then
      FArgs.Assign( Args );
end;



destructor TVCall.Destroy;
begin
   FArgs.Free;
   inherited Destroy;
end;


procedure TVCall.FillDump( V: TVson );
begin
   V.Items[0].Handle := TVson.CreateHandle( VC.Command, VC.Call );
   V.Items[1].Handle := FFunc.Handle;
   V.Items[2].Assign( FArgs );
end;

end.

