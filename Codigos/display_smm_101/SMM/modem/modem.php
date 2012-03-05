<?php
	
	include "../commun/connection.php";
	include "../commun/constants.php";
	include "../SQLs/modem/modem.inc.php";
	
	// Separa a senten�a RMC do protocolo NMEA 0186
	list(, $vgsGpsHour, $vgsGpsDadaValid, $vgsGpsLatitude, $vgsGpsLatitudeHemisphere, $vgsGpsLongitude, $vgsGpsLongitudeHemisphere,,, $vgsGpsDate ) = explode ( ",", $_GET[ nmeaRmc ] );
	
	// Converte da data e hora do GPS no formate DATETIME do MySql
	$vgsGpsDate		 =  preg_replace ( "/([0-9]{2})([0-9]{2})([0-9]{2})/", "20$3/$2/$1", $vgsGpsDate );
	$vgsGpsHour		 =  preg_replace ( "/([0-9]{2})([0-9]{2})([0-9]{2})([.0-9]+)/", "$1:$2:$3", $vgsGpsHour );
	$vgsGpsDateTime	 =  $vgsGpsDate . " " . $vgsGpsHour;
	
	// Limpa a mem�ria
	unset ( $vgsGpsDate, $vgsGpsHour );
	
	// Caso a mensagem tenha ID igual a 0 ( zero ), isto indica que o equipamento foi iniciado / reset, por�m, o banco de 
	// dados n�o aceita o valor zero por causa do relacionamento das chaves. Por isto, o mesmo � convertido para NULL
	if ( $_GET[messageId] == 0 )
	{
		$_GET[messageId] = "NULL";
	}
	
	// Arquiva do dados enviados pelo equipamento na tabela de log.
	// Perceba que a presen�a das informa��es referentes a latitude e longitude de acordo com o indicador de dado 
	// v�lido do protocolo NMEA depende do fabricante do equipamento, podendo ser enviada a posi��o vazia ou a ultima 
	// informa��o v�lida.
	$vgoDb->query	(
						array 	(
									"query" =>  insertGpdData ( $_GET[deviceId], $_GET[messageId], $_GET[gprsSignalQuality], $_GET[powerControl], $vgsGpsDateTime, $vgsGpsLatitude, $vgsGpsLatitudeHemisphere, $vgsGpsLongitude, $vgsGpsLongitudeHemisphere, $vgsGpsDadaValid ),
									"line"	=>  __LINE__,
									"file"	=>  __FILE__
								)
					);
	
	// Detecta se o equipamento foi iniciado/reset pelo id da mensagem. Onde o valor 0 ( zero ) indica o reset do equipamento 
	// e um valor diferente de 0 ( zero ) indica a �ltima mensagem recebida pelo mesmo.
	if ( $_GET[ messageId ] == onResetId )
	{   
		// Para facilitar a instala��o em campo, h� a possibilidade de se envia uma mensagem especial no momento da primeira 
		// conex�o, ap�s uma opera��o de reset, permitindo ao instalador em campo perceber se o equipamento se conectou a 
		// internet corretamente ou n�o.
		$vgaDataDb =  getOnConnectMessage ();
	}
	
	else
	{
		// Captura a mensagem a ser enviada ao equipamento
		$vgaDataDb =  getNormalMessage ();
	}
	
	// Imprime na tela os dados que v�o ser lidos pelo equipamento em campo.
	// Perceba que obrigatoriamente, toda String deve terminar em \r \n ou 'Carriage Return' ( 0x0D ) 'Line Feed' ( 0x0A )
	// Para mais informa��es sobre o protocolo usado no equipamento, consulte a documenta��o do mesmo na pasta do projeto.
	
	// Limpa a mem�ria RAM do equipamento
	// Campo obrigat�rio na primeira linha de transmiss�o de dados sobe pena de estouro da pilha
	print "clear-all\r\n";
	
	// Imprime o ID ( unsigned int not zero ) da mensagem atual
	// Perceba que apenas uma mensagem � enviada ao equipamento por ID recebido, sendo obrigat�rio o incremento do ID para que uma 
	// pr�xima mensagem possa ser enviada pelo equipamento.
	print "s:{$vgaDataDb['Message'][0]['Mensagem_Id']}\r\n";
	
	// Texto ASCII a ser enviado pela porta serial do equipamento
	print "m:{$vgaDataDb['Message'][0]['Mensagem_Texto']}\r\n";
	
	// Configura��es adicionais do equipamento se forem necess�rias
	if ( is_array ( $vgaDataDb['Config'] ) )
	{
		foreach ( $vgaDataDb['Config'] as $vgaConfig )
		{
			print "{$vgaConfig['EquipamentoParametro_RotuloEquipamento']}:{$vgaConfig['EquipamentoParametro_Valor']}\r\n";
		}
	}
	
	// String obrigat�ria no fim da transmiss�o para acusar o fim do recebimento e enviar a mensagem do equipamento para o dispositivo 
	// externo.
	print "end-data\r\n";
	
	// Limpa a mem�ria
	unset ( $vgaConfig, $vgaDataDb );
	
	/** Recebe do banco de dados a mensagem de conex�o do equipamento, caso a mesma se aplique, e demais par�metros de configura��o.
	 *  @param void
	 *  @return Array - Array (
	 *                            Message - Array unit�rio contendo a mensagem a ser enviado ao equipamento
	 *                            Config  - Array unidemencional, de �ndice n, contendo as configura��es do equipamento / dispositivo externo, 
	 *                                      caso as mesmas se apliquem.
	 *                        )
	 */
	function getOnConnectMessage ()
	{
		global $vgoDb;
		
		$vlaSetup		 =  array	(
										"query"		 => selectDeviceConfigByDeviceIdAndDeviceType ( $_GET[ deviceId ] ), //"SELECT EquipamentoParametro.EquipamentoParametro_RotuloEquipamento, EquipamentoParametro.EquipamentoParametro_Valor FROM EquipamentoParametro LEFT JOIN ( Equipamento, EquipamentoHasEquipamentoParametro, EquipamentoTipoHasEquipamentoParametro ) ON ( ( EquipamentoHasEquipamentoParametro.EquipamentoHasEquipamentoParametro_Equipamento_Id = {$_GET[ deviceId ]} AND EquipamentoHasEquipamentoParametro.EquipamentoHasEquipamentoParametro_EquipamentoParametro_Id = EquipamentoParametro.EquipamentoParametro_Id ) OR ( EquipamentoTipoHasEquipamentoParametro.EquipamentoTipoHasEquipamentoParametro_EquipamentoTipo_Id = Equipamento.Equipamento_EquipamentoTipo_Id AND Equipamento.Equipamento_Id = {$_GET[ deviceId ]} AND EquipamentoHasEquipamentoParametro.EquipamentoHasEquipamentoParametro_EquipamentoParametro_Id = EquipamentoParametro.EquipamentoParametro_Id ) )",
										"function"	 => __FUNCTION__,
										"line"		 => __LINE__ 
									);
							$vgoDb->query	( $vlaSetup );
		$vlaLineConfig	 =  $vgoDb->fetch ();
		
		$vlaSetup		 =  array	(
										"query"		 => selectOnConnectMesage ( $_GET[ deviceId ] ), //"SELECT Mensagem.Mensagem_Id, Mensagem.Mensagem_Texto FROM Mensagem WHERE Mensagem_Equipamento_Id = {$_GET[ deviceId ]} AND Mensagem_MensagemTipo_Id = (SELECT MensagemTipo_Id FROM MensagemTipo WHERE MensagemTipo.MensagemTipo_Valor = 'onConnect' ) /*AND Mensagem.Mensagem_DataInicial >= NOW() AND Mensagem.Mensagem_DataFinal <= NOW()*/",
										"function"	 => __FUNCTION__,
										"line"		 => __LINE__ 
									);
									
							$vgoDb->query	( $vlaSetup );
		$vlaLineMesage	 =  $vgoDb->fetch ();
		
		return array	(
							"Message"	=>  $vlaLineMesage,
							"Config"	=>  $vlaLineConfig
						);
	}
	
	/** Recebe do banco de dados a mensagem normal do equipamento e demais par�metros de configura��o.
	 *  @param void
	 *  @return Array - Array (
	 *                            Message - Array unit�rio contendo a mensagem a ser enviado ao equipamento
	 *                            Config  - Array unidemencional, de �ndice n, contendo as configura��es do equipamento / dispositivo externo, 
	 *                                      caso as mesmas se apliquem.
	 *                        )
	 */
	function getNormalMessage ()
	{
		// O sistema foi projetado para que todas as mensagens enviadas ao equipamento sejam controladas por data, por�m,
		// foi adicionado um controle para se enviar uma mensagem padr�o caso haja um erro no calculo de datas e o equipamento 
		// fique sem mensagem v�lida para a data em quest�o.
		global $vgoDb;
		
		$vlaSetup		 =  array	(
										"query"		 => selectDeviceConfigByDeviceIdAndDeviceType ( $_GET[ deviceId ] ), //"SELECT EquipamentoParametro.EquipamentoParametro_RotuloEquipamento, EquipamentoParametro.EquipamentoParametro_Valor FROM EquipamentoParametro LEFT JOIN ( Equipamento, EquipamentoHasEquipamentoParametro, EquipamentoTipoHasEquipamentoParametro ) ON ( ( EquipamentoHasEquipamentoParametro.EquipamentoHasEquipamentoParametro_Equipamento_Id = {$_GET[ deviceId ]} AND EquipamentoHasEquipamentoParametro.EquipamentoHasEquipamentoParametro_EquipamentoParametro_Id = EquipamentoParametro.EquipamentoParametro_Id ) OR ( EquipamentoTipoHasEquipamentoParametro.EquipamentoTipoHasEquipamentoParametro_EquipamentoTipo_Id = Equipamento.Equipamento_EquipamentoTipo_Id AND Equipamento.Equipamento_Id = {$_GET[ deviceId ]} AND EquipamentoHasEquipamentoParametro.EquipamentoHasEquipamentoParametro_EquipamentoParametro_Id = EquipamentoParametro.EquipamentoParametro_Id ) )",
										"function"	 => __FUNCTION__,
										"line"		 => __LINE__ 
									);
							$vgoDb->query	( $vlaSetup );
		$vlaLineConfig	 =  $vgoDb->fetch ();
		
		$vlaSetup		 =  array	(
										"query"		 => selectNormalMessage ( $_GET[ deviceId ] ), //"SELECT Mensagem.Mensagem_Id, Mensagem.Mensagem_Texto FROM Mensagem WHERE Mensagem_Equipamento_Id = {$_GET[ deviceId ]} AND Mensagem_MensagemTipo_Id = (SELECT MensagemTipo_Id FROM MensagemTipo WHERE MensagemTipo.MensagemTipo_Valor = 'dateNormal' ) AND Mensagem.Mensagem_DataInicial <= NOW() AND Mensagem.Mensagem_DataFinal > NOW() ORDER BY Mensagem.Mensagem_Id DESC LIMIT 1",
										"function"	 => __FUNCTION__,
										"line"		 => __LINE__ 
									);
							$vgoDb->query	( $vlaSetup );
		$vlaLineMesage	 =  $vgoDb->fetch ();
		
		// Testa se foi encontrada uma mensagem v�lida para a data atual
		if ( is_array ( $vlaLineMesage ) )
		{
			if ( count ( $vlaLineMesage ) >= 1 )
			{
				return array	(
									"Message"	=>  $vlaLineMesage,
									"Config"	=>  $vlaLineConfig
								);
			}
		}
		
		// Pega no banco de dados a mensagem padr�o caso haja um erro de data
		$vlaSetup		 =  array	(
										"query"		 => selectOnDateErroMessage ( $_GET[ deviceId ] ), //"SELECT Mensagem.Mensagem_Id, Mensagem.Mensagem_Texto FROM Mensagem WHERE Mensagem_Equipamento_Id = {$_GET[ deviceId ]} AND Mensagem_MensagemTipo_Id = (SELECT MensagemTipo_Id FROM MensagemTipo WHERE MensagemTipo.MensagemTipo_Valor = 'dateError' )",
										"function"	 => __FUNCTION__,
										"line"		 => __LINE__ 
									);
							$vgoDb->query	( $vlaSetup );
		$vlaLineMesage	 =  $vgoDb->fetch ();
		
		return array	(
							"Message"	=>  $vlaLineMesage,
							"Config"	=>  $vlaLineConfig
						);
	}
	