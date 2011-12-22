#ifndef ModemATBased_h
#define ModemATBased_h

#include "WProgram.h"

/*extern "C"
{
	typedef void (*voidPonteiroDeFuncao)( unsigned char );
}*/

#define debug_ModemATBased

//#define kTelefon    0x80
//#define kMessage    0x81
//#define kAnyWare    0x82

/*! \brief All Arduino Serial Ports.
 *  Sets the serial port used to communicate with the module.
 *  The UBRRxH are AVR registers used by serial port.
 *  
 *  Define a porta de serial usada para se comunicar com o módulo.
 *  UBRRxH são registros do AVR usados para configurar a porta serial
 *  
 */
namespace SerialPort
{
	enum eSerialPort
	{
		#if defined(UBRR3H)
		Port3,
		#endif
		#if defined(UBRR2H)
		Port2,
		#endif
		#if defined(UBRR1H)
		Port1,
		#endif
		#ifndef debug_sayitmodule
		Port0
		#else
		#warning SayItModule::SerialPort0 is used by debuger. Please, open SayItModule.h and comment line #define debug_sayitmodule 1
		#warning SayItModule::SerialPort0 esta sendo usada pelo debuger. Por favor, abra o arquivo SayItModule.h e comente a linha #define debug_sayitmodule 1
		#endif
	};
}
using namespace SerialPort;

class ModemATBased
{
	private:
	
        static void ( * vcpfOnFunction ) ( void );
    
		static eSerialPort		vceSerial;
		static const String *	vcacucATString[ 20 ];
		static const String *	vcacucATResponse[ 20 ];
		static const String *	vcucpDataToCompare;
		static unsigned char	vcucSMStep;
        static unsigned char    vcucSMStepCompare;
        static unsigned char    vcucSMTotalStep;
        static          char    vcascPointerDataModem;
    
		static void				sendData ( String vapucData );
		static int				availableData ( );
		static unsigned char	getData ( );
		static void				StateMachineRun ();
		
	public:
        static const unsigned char *    vcpucTelefone;
        static const unsigned char *    vcpucMessage;
        static String                   QueryString;
        
                                ModemATBased ();
		static void				setSerial ( eSerialPort vaeSerial );
		static void				sendTextSms ( const String * vapcucTelefon, const String * vapcucMessage, void ( * vafpExtFuntion ) ( void ) );
        static void             internetDataSendByGET ( const String * vapsHost, const String * vapsHostPort, void ( * vafpExtFuntion ) ( void ) );
        static void             internetDisconnectToHost ( void ( * vafpExtFuntion ) ( void ) );
        static void             internetConnectToHost ( const String * vapsHost, const String * vapsHostPort, void ( * vafpExtFuntion ) ( void ) );
        static void             internetConnect ( void ( * vafpExtFuntion ) ( void ) );
		static void				getDataModem ();
};

#endif