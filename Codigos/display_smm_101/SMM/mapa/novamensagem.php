<font face=\'verdana\' size=\'1\'>
	<form name=\'newMessage\'>
		<select name=\'command\' onChange=\'onChangeCommand(this.value)\'>
			<option value=\'[H]\'>Mostrar hora</option>
			<option value=\'[D]\'>Mostrar data</option>
			<option value=\'[T]\'>Mostrar temperatura</option>
			<option value=\'[P]\'>Pausa</option>
			<option value=\'[L]\'>Limpar display</option>
			<option value=\'[Z]\'>Apagar mem�ria</option>
			<option value=\'M\'>Mensagem fixa</option>
			<option value=\'B\'>Piscar mensagem</option>
			<option value=\'C\'>Centro</option>
			<option value=\'E\'>Abrir pelo lado esquerdo</option>
			<option value=\'G\'>Abrir pelo lado direito</option>
			<option value=\'J\'>Jogar mensagem</option>
			<option value=\'[K]\'>Kome-Kome</option>
			<option value=\'R\'>Rolar mensagem</option>
			<option value=\'S\'>Subir mensagem</option>
			<option value=\'I\'>Descer mensagem</option>
			<option value=\'A\'>Acertar o rel�gio</option>
		</select>
		<select name=\'complementPause\' style="visibility:hidden;">
			<option value=\'1\'>1 segundos</option>
			<option value=\'2\'>2 segundos</option>
			<option value=\'3\'>3 segundos</option>
			<option value=\'4\'>4 segundos</option>
			<option value=\'5\'>5 segundos</option>
			<option value=\'6\'>6 segundos</option>
			<option value=\'7\'>7 segundos</option>
			<option value=\'8\'>8 segundos</option>
			<option value=\'9\'>9 segundos</option>
		</select>
		<select name=\'complementRoll\' style="visibility:hidden;">
			<option value=\'R\'>Velocidade r�pida</option>
			<option value=\'M\'>Velocidade m�dia</option>
			<option value=\'L\'>Velocidade lenta</option>
		</select>
	</form>
</font>