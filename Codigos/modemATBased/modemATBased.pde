//#define debug_ModemATBased

#include <ModemATBased.h>

const String host         =  "kemper.com.br";
const String hostPort     =  String ( 80, DEC );
const String queryString  =  "/modem/modem.php?contador=";

unsigned int contadorLoop          =  0;

void setup ()
{
  Serial.begin ( 19200 );
  
  while (true)
  {
    if (Serial.available())
      if ((unsigned char)Serial.read()=='c')
        break;
  }
  
  ModemATBased::StateMachineEvent = &Evento;
  ModemATBased::setSerial ( SerialPort::Port1, 19200 );
  //ModemATBased::internetConnect ();
  
  //ModemATBased::Telefon =  "99268744";
  //ModemATBased::Message =  "Hello World! In Brasil, this message can be 128 characters per message";
  //ModemATBased::sendTextSms ( &pisca );
}

void Evento ( eEvent e )
{
  switch ( e )
  {
    case Event::Ring:      Serial.println ( "tocando\r" ); break;
    case Event::NoCarrier: Serial.println ( "Parou de tocar\r" ); break;
    case Event::InternetConnect:
      Serial.println ( "internet connected" );
      ModemATBased::Host        =  "kemper.com.br";
      ModemATBased::HostPort    =  "80";
      ModemATBased::internetConnectToHost ();
      break;
      
    case Event::InternetConnectToHost:
    case Event::InternetDataSendByGET:
      Serial.println ( "sent data" );
      ModemATBased::QueryString =  "/modem/modem.php?status=";
      ModemATBased::QueryString.concat ( String ( contadorLoop, DEC ) );
      ModemATBased::internetDataSendByGET ();
      contadorLoop ++;
      break;
      
    case Event::Closed: pisca(); break;
    case Event::Error:  pisca(); break;
    case Event::Close:  pisca(); break;
  }
}

void pisca ()
{
  while ( true )
  {
    pinMode ( 13, OUTPUT );
    digitalWrite ( 13, HIGH );
    delay ( 300 );
    digitalWrite ( 13, LOW );
    delay ( 300 );
  }
}

void loop ()
{
  ModemATBased::getDataModem ();
}
