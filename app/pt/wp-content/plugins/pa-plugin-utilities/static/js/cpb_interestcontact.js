jQuery(document).ready(function($){
	var json;
	var form = $('#frm-matric');
	var uf = {}, city = {}, school = {};
	var controllerMatricula = "http://matricula.educacaoadventista.org.br/v2/controller/ctrl_matricula.php";

	initFunctions();

	function initFunctions(){
		getJson();
		validateForm();
		changeBox();
		sendForm();
	}

	function changeBox(){
		$('#bx-estado').change(function(){
			var idUf = $(this).val();
			$('#bx-cidade').empty().append('<option value="0">Cidade</option>');
			$('#bx-entidade').empty().append('<option value="0">Escola</option>');

			if(idUf != 0){
				$.when(extractCityFromJson(idUf)).then(loadBxCity());
			}
		});

		$('#bx-cidade').change(function(){
			var idCity = $(this).val();

			$('#bx-entidade').empty().append('<option value="0">Escola</option>');

			if(idCity != 0){
				$.when(extractSchoolFromJson(idCity)).then(loadBxSchool());
			}
		});

		$('#bx-entidade').change(function(){
			var idEntidade = $(this).val();

			$('#bx-serie').empty().append('<option value="0">Ano</option>');

			if(idEntidade)
				getSeries(idEntidade);
		});
	}

	function chageSelectBorderColor(value)
	{
		if(value == 0)
			$('select.error').parent('.sct-matric').css('border-color', '#ffcc00');
		else
			$('select.valid').parent('.sct-matric').css('border-color', '#e0d8cd');
	}

	function sendForm(){
		form.submit(function( event ) {
			if (form.valid()) {
				const idUniao = getUniao();
				var params = $(this).serialize(),
					codEscola = $('#bx-entidade option:selected').data('codEscola'),
					entidade = $('#bx-entidade option:selected').data('entidade');

				params += '&idUniao='+$('#bx-entidade option:selected').data('idUniao');
				params += '&idAssociacao='+$('#bx-entidade option:selected').data('idAssociacao');
				params += '&idEntidade='+$('#bx-entidade option:selected').data('idEntidade');
				params += '&desc_serie=' + $('#bx-serie option:selected').text();
				params += '&cod_escola=' + codEscola;
				params += '&entidade=' + entidade;
				params += '&escola=' + entidade + "-" + codEscola;
				params += '&origem=6';
				params += '&sexo=1';

				$.ajax({
					type : 'POST',
					url: controllerMatricula,
					data: 'action=salvaCad&'+params,
					dataType: 'json',
					contentType: 'application/x-www-form-urlencoded',
					beforeSend: function() {
						$('.btn').attr('disabled', 'disabled');
					},
					success: function(resp){
						if (resp.status) {
							$('.result-matric').html("Deu tudo certo, já recebemos as suas informações.");
							$('.result-matric').css("border", "2px solid #398f14");
							$('#frm-matric').get(0).reset();
							printAdwords(idUniao);
						} 
						else {
							$('.result-matric').html("Ops, não consegui enviar, tente novamente em cinco minutos.");
							$('.result-matric').css("border", "2px solid #FF0000");
						}
						$('.btn').removeAttr('disabled', 'disabled');
					}
				});
			}
			else {
				chageSelectBorderColor(0);
				$('.result-matric').html("Enquanto não estiver tudo preenchido corretamente não vou enviar.");
				$('.result-matric').css("border", "2px solid #ffcc00");
			}

			event.preventDefault();
		});
	}

	function validateForm(){
		form.validate({
			rules: {
				estado:		{selectcheck: true},
				cidade:		{selectcheck: true},
				id_escola:	{selectcheck: true},
				serie:		{selectcheck: true},
				nomealuno:	{required: true},
				nomeresp:	{required: true},
				email:		{required: true},
				telefone:	{required: true}
			},
			messages: {
				estado:		'',
				cidade:		'',
				id_escola:	'',
				serie:		'',
				nomealuno:	'',
				nomeresp:	'',
				email:		'',
				telefone:	''
			}
		});

		jQuery.validator.addMethod('selectcheck', function (value) {
			chageSelectBorderColor(value);
			return (value != '0');
		}, 'required');
	}

	function getJson(){
		$.ajax({
			type : 'POST',
			url: controllerMatricula,
			data: 'action=listaTodasEscola',
			dataType: 'json',
			contentType: 'application/x-www-form-urlencoded',
			success: function(resp){                                                                                
				json = resp;
				$.when(extractUfFromJson()).then(loadBxUf());
			}
		});
	}

	/**
	 * Retorna todas as séries da entidade selecionada.
	 * Monta os options no select de séries com as informações retornadas da controller de matrícula.
	 *
	 * @param int idEntidade - O identificador único de uma entidade em db_usuario.entidade.
	 * @return string
	 * @author Casa Publicadora Brasileira - Davi Aragão
	 **/
	function getSeries (idEntidade)
	{
		$.ajax({
			type : 'POST',
			url: controllerMatricula,
			data: 'action=listaSerie&type=option-json&id_escola=' + idEntidade,
			dataType: 'json',
			contentType: 'application/x-www-form-urlencoded',
			success: resp => $("#bx-serie").append(resp.reduce((options, serie) => options + "<option value='" + serie.id + "'>" + serie.descr + "</option>"))
		});
	}

	function extractUfFromJson(){
		$.each(json, function(i, v) {
			obj = {};
			obj['id'] = v.idUf;
			obj['desc'] = v.siglaUf;
			key = v.idUf+'-'+v.siglaUf;

			if (!(key in uf)) {
				uf[key] = obj;
			}
		});
	}

	function extractCityFromJson(idUf){
		city = {};
		$.each(json, function(i, v) {
			obj = {};
			obj['id'] = v.idCidade;
			obj['desc'] = v.descCidade;
			key = v.idCidade+'-'+v.descCidade;

			if (!(key in city) && v.idUf == idUf) {
				city[key] = obj;
			}
		});
	}

	function extractSchoolFromJson(idCity){
		school = {};
		$.each(json, function(i, v) {
			obj = {};
			obj['id'] = v.idEntidade;
			obj['desc'] = v.descEntidade;
			obj['dominio'] = v.dominio;
			obj['idUniao'] = v.idUniao;
			obj['abrevUniao'] = v.abrevUniao;
			obj['idAssociacao'] = v.idAssociacao;
			obj['idEntidade'] = v.idEntidade;
			obj['descAssociacao'] = v.descAssociacao;
			obj['codEscola'] = v.codEscola;
			obj['entidade'] = v.entidade;
			key = v.idEntidade+'-'+v.descEntidade;

			if (!(key in school) && v.idCidade == idCity) {
				school[key] = obj;
			}
		});
	}

	function loadBxUf(){
		$.each(uf, function(i, v) {
			$('#bx-estado').append($('<option value="'+v.id+'">'+v.desc+'</option>'));
		});
	}

	function loadBxCity(){
		$.each(city, function(i, v) {
			$('#bx-cidade').append($('<option value="'+v.id+'">'+v.desc+'</option>'));
		});
	}

	function loadBxSchool(){
		$.each(school, function(i, v) {
			$('#bx-entidade').append($('<option data-dominio="'+v.dominio+'" data-id-uniao="'+v.idUniao+'" data-desc-uniao="'+v.abrevUniao+'" data-id-associacao="'+v.idAssociacao+'" data-desc-associacao="'+v.descAssociacao+'" data-id-entidade="'+v.idEntidade+'" data-cod-escola="'+ v.codEscola +'" data-entidade="'+ v.entidade +'" value="'+v.id+'">'+v.desc+'</option>'));
		});
	}

	/**
	 * Coloca os scripts para o GoogleAdWords no documento.
	 *
	 * @return
	 * @author Casa Publicadora Brasileira - Davi Aragão
	 **/
	const printAdwords = (idUniao) => {
		createAdWordsScript(getAdWordsNumbers(idUniao));//Por União
		createAdWordsScript({'id': 954264341, 'label': "qL9gCPbWqF8QldaDxwM"});//Geral
	}

	/**
	 * Monta e adiciona um sript do AdWords com os parâmetros de adWordsNumbers no documento.
	 *
	 * @param Object adWordsNumbers - id e label do google_conversion.
	 * @return
	 * @author Casa Publicadora Brasileira - Davi Aragão
	 **/
	const createAdWordsScript = adWordsNumbers => {
		/* <![CDATA[ */
		let google_conversion_id = adWordsNumbers.id;
		let google_conversion_language = "en";
		let google_conversion_format = "3";
		let google_conversion_color = "ffffff";
		let google_conversion_label = adWordsNumbers.label;
		let google_remarketing_only = false;
		/* ]]> */

		$.getScript('//www.googleadservices.com/pagead/conversion.js');

		let image = new Image(1, 1);
		image.src = "//www.googleadservices.com/pagead/conversion/"+google_conversion_id+"/?label="+google_conversion_label+"&guid=ON&script=0";
		/* Conversion Tracking End */
	}

	/**
	 * Define o google_conversion_id e o google_conversion_label com base no idUniao.
	 *
	 * @param int idUniao - O identificador da união.
	 * @return
	 * @author Casa Publicadora Brasileira - Davi Aragão
	 **/
	const getAdWordsNumbers = idUniao => {
		/*
		 * União tem o identificador únicao da entidade em db_usuario.entidade.
		 * 3 - ULB
		 * 4 - USB
		 * 5 - USEB
		 * 6 - UNEB
		 * 7 - UNOB
		 * 8 - UCOB
		 * 9 - UCB
		 * 10 - UNB
		 * */
		switch (Number(idUniao)) {
			case 3:
				conversionID = 850253162;
				conversionLabel = "_-82CJD73nIQ6qq3lQM";
				break;
			case 4:
				conversionID = 849789251;
				conversionLabel = "7pK7CJao5nIQw4KblQM";
				break;
			case 5:
				conversionID = 849233600;
				conversionLabel = "4mPkCKa2zXIQwI35lAM";
				break;
			case 6:
				conversionID = 849306365;
				conversionLabel = "5yXBCLve5XIQ_cX9lAM";
				break;
			case 7:
				conversionID = 849484205;
				conversionLabel = "peppCICizXIQrbOIlQM";
				break;
			case 8:
				conversionID = 849499597;
				conversionLabel = "SyDdCJy4ynIQzauJlQM";
				break;
			case 9:
				conversionID = 849839142;
				conversionLabel = "55I1CLPE23IQpoielQM";
				break;
			case 10:
				conversionID = 850148215;
				conversionLabel = "CDqYCO7Z3HIQ9_awlQM";
				break;
		}

		return {
			'id': conversionID,
			'label': conversionLabel
		};
	}

	/**
	 * Recupera o identicador da união da entidade selecionada em #bx-entidade.
	 *
	 * @param 
	 * @return int
	 * @author Casa Publicadora Brasileira - Davi Aragão
	 * @since 11/10/2017
	 **/
	const getUniao = () => {
		const select = document.querySelector("#bx-entidade");
		const uniao = select.options[select.selectedIndex].dataset.idUniao;
		return uniao;
	}
});
