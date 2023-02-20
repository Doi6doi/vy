unit uvs;

interface

uses
  Classes, SysUtils, uvson;

type
   EVS = class( Exception );

   TVSys = class;
   TVFunc = class;

   /// objektum
   TVObj = class
   protected
      FHandle: TVHandle;
   public
      constructor CreateHandle( H: TVHandle );
   end;

   /// interfész
   TVIntf = class
   protected
      FPkg: Utf8String;
      FName: Utf8String;
      FVersion: Utf8String;
      FHandle: TVHandle;
      FFuncs: TStringList;
      function GetFullName: Utf8String;
      procedure SetFullName( Value: Utf8String );
   public
      constructor Create;
      destructor Destroy; override;
      function Func( Name: Utf8String; Check: Boolean = false ): TVFunc;
      property FullName: Utf8String read GetFullName write SetFullName;
      property Pkg: Utf8String read FPkg write FPkg;
      property Name: Utf8String read FName write FName;
      property Version: Utf8String read FVersion write FVersion;
      property Handle: TVHandle read FHandle write FHandle;
   end;

   /// függvény
   TVFunc = class
   protected
      FHandle: TVHandle;
   public
      function Local: Boolean;
      function Execute( Args: TVson ): TVson; virtual; abstract;
      property Handle: TVHandle read FHandle;
   end;

   TVIntfEvent = procedure( Sys: TVSys; Intf: TVIntf; Enable: Boolean ) of object;
   TVObjEvent = procedure( Sys: TVSys; Obj: TVObj; Enable: Boolean ) of object;

   TVSys = class
   protected
      FIntfs: TStringList;
      FObjs: TStringList;
      FOnIntf: TVIntfEvent;
      FOnObj: TVObjEvent;
      FObjects: TVFunc;
      procedure DelIntf( I: Integer );
      procedure DelObj( I: Integer );
   public
      constructor Create;
      destructor Destroy; override;
      procedure Clear;
      procedure ClearIntfs;
      procedure ClearObjs;
      function AddIntf: TVIntf;
      function FindIntf( Pkg, Name, Version: Utf8String; Check: Boolean ): TVIntf;
      function Objects: TVFunc;
      procedure AddObj( Obj: TVObj );
      property OnIntf: TVIntfEvent read FOnIntf write FOnIntf;
      property OnObj: TVObjEvent read FOnObj write FOnObj;
   end;

   TVConsts = class
      /// fajták
      Command: Integer;
      Intf: Integer;
      ClientFunc: Integer;
      /// parancsok
      Helo: Integer;
      Call: Integer;
      Result: Integer;
      /// stringek
      VS: Utf8String;
      Reflect: Utf8String;
      Objects: Utf8String;
   end;

var
   VC: TVConsts;

implementation

uses
   StrUtils;

constructor TVSys.Create;
begin
   FIntfs := TStringList.Create;
   FIntfs.OwnsObjects := true;
   FOnIntf := nil;
   FOnObj := nil;
   FObjects := nil;
end;


destructor TVSys.Destroy;
begin
   FreeAndNil( FIntfs );
end;


procedure TVSys.Clear;
begin
   ClearIntfs;
   ClearObjs;
end;


procedure TVSys.ClearIntfs;
var
   i: Integer;
begin
   for i := FIntfs.Count-1 downto 0 do
      DelIntf(i);
   FObjects := nil;
end;


procedure TVSys.ClearObjs;
var
   i: Integer;
begin
   for i:= FObjs.Count-1 downto 0 do
      DelObj(i);
end;


function TVSys.AddIntf: TVIntf;
begin
   Result := TVIntf.Create;
   FIntfs.AddObject( '', Result );
   if Assigned( FOnIntf ) then
      FOnIntf( Self, Result, true );
end;


function TVSys.FindIntf( Pkg, Name, Version: Utf8String; Check: Boolean ): TVIntf;
var
   I: Integer;
   II: TVIntf;
begin
   Result := nil;
   for i := 0 to FIntfs.Count-1 do begin
      II := TVIntf( FIntfs.Objects[i] );
      if Name = II.Name then begin
         if (''=Pkg) or (II.Pkg = Pkg) then begin
            if ('' = Version) or (II.Version = Version) then begin
               if (nil=Result) or (Result.Version < II.Version) then
                  Result := II;
            end;
         end;
      end;
   end;
   if Check and (nil = Result) then
      raise EVS.CreateFmt('Could not find interface %s:%s:%s',
         [Pkg, Name, Version] );
end;

function TVSys.Objects: TVFunc;
var
   II: TVIntf;
begin
   if nil = FObjects then begin
      II := FindIntf( VC.Vs, VC.Reflect, '', true );
      FObjects := II.Func( VC.Objects, true );
   end;
   Result := FObjects;
end;

procedure TVSys.AddObj( Obj: TVObj );
begin
   FObjs.AddObject( '', Obj );
   if Assigned( FOnObj ) then
      FOnObj( Self, Obj, true );
end;


procedure TVSys.DelIntf( I: Integer );
var
   VI: TVIntf;
begin
   VI := FIntfs.Objects[i] as TVIntf;
   if Assigned( FOnIntf ) then
      FOnIntf( Self, VI, false );
   FIntfs.Delete(i);
end;


procedure TVSys.DelObj( I: Integer );
var
   VO: TVObj;
begin
   VO := FObjs.Objects[i] as TVObj;
   if Assigned( FOnObj ) then
      FOnObj( Self, VO, false );
   FObjs.Delete(i);
end;


constructor TVIntf.Create;
begin
   FPkg := '';
   FName := '';
   FVersion := '';
   FHandle.Kind := VC.Intf;
   FHandle.Value := 0;
   FFuncs := TStringList.Create;
   FFuncs.OwnsObjects := true;
end;


destructor TVIntf.Destroy;
begin
   FreeAndNil( FFuncs );
end;


function TVIntf.GetFullName: Utf8String;
begin
   Result := FName;
   if '' <> FPkg then
      Result := FPkg+':'+Result;
   if '' <> FVersion then
      Result := Result +':'+FVersion;
end;


procedure TVIntf.SetFullName( Value: Utf8String );
var
   I,J: Integer;
begin
   I := Pos(':', Value);
   if 0 < I then begin
      J := PosEx( ':', Value, I+1 );
   end else begin
      I := Length(Value)+1;
      J := 0;
   end;
   if 0 = J then
      J := Length(Value)+1;
   FPkg := Copy( Value, 1, I-1 );
   FName := Copy( Value, I+1, J-I-1 );
   FVersion := Copy( Value, J+1 );
end;


function TVIntf.Func( Name: Utf8String; Check: Boolean ): TVFunc;
var
   i: Integer;
begin
   i := FFuncs.IndexOf( Name );
   if (0 > i) and Check then
      raise EVS.CreateFmt('Unknown function: %s', [Name] );
   Result := TVFunc( FFuncs.Objects[i] );
end;



constructor TVObj.CreateHandle( H: TVHandle );
begin
   FHandle := H;
end;


function TVFunc.Local: Boolean;
begin
   Result := FHandle.Kind = VC.ClientFunc;
end;

initialization
   VC := TVConsts.Create;

   VC.Command := 1;
   VC.Intf := 2;

   VC.Helo := 1;
   VC.Call := 2;
   VC.Result := 3;

   VC.VS := 'vs';
   VC.Reflect := 'reflect';
   VC.Objects := 'objects';
end.

