unit uvconn;

interface

uses
  Classes, SysUtils, ubstream, uvs, uvson;

type

   { TVConn }

   TVConn = class
   protected
      FVS: TVson;
      FVSys: TVSys;
      FBS: TBStream;
      procedure SetStream( AValue: TStream );
   public
      constructor Create( AVSys: TVSys );
      destructor Destroy; override;
      /// kezdeti interfészek beolvasása
      procedure ReadInit;
      /// a csatorna
      property Stream: TStream write SetStream;
   end;

implementation

uses
   ugui;

procedure TVConn.SetStream(AValue: TStream);
begin
   FBS.Base := AValue;
end;

constructor TVConn.Create( AVSys: TVSys );
begin
   FVS := TVson.Create;
   FVSys := AVSys;
   FBS := TBStream.Create(nil);
end;

destructor TVConn.Destroy;
begin
   FBS.Free;
   FVS.Free;
end;

procedure TVConn.ReadInit;
begin
   FVS.Read( FBS );
   TGui.Info('nyf');
end;

end.
                     1
