<?php
	
	if ($_GET["lang"] == "pt_BR"){
		$titulo = "TV Novo Tempo";
		$programacao = "Veja a programação";
	} else {
		$titulo = "TV Nuevo Tiempo";
		$programacao = "Vea la programación";
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $titulo; ?></title>
<link href="<?php echo $_GET["dir"]; ?>/flavours/static/css/player_nt.css" rel="stylesheet" type="text/css" />
<link href="http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic" rel="stylesheet" type="text/css">
<link href="http://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">	
</head>

<body class="tvnt">

	<div class="<?php echo $_GET["lang"]; ?> container-tvnt">

		<?php 
		
		$source = $_GET["tv"];

		switch ($source) {
		    case "portugues":
		        $source = "src=http%3A%2F%2Fstream.novotempo.com%3A1935%2Ftv%2Fsmil%3Atvnovotempo.smil%2Fmanifest.f4m&loop=true&autoPlay=true&streamType=live";
		       	$link_programacao = "http://novotempo.com/tv/grade/";
		        break;
		    case "espanhol":
		        $source = "src=http%3A%2F%2Fstream.novotempo.com%3A1935%2Ftv%2Fsmil%3Atvnuevotiempo.smil%2Fmanifest.f4m&loop=true&autoPlay=true&streamType=live";
		        $link_programacao = "http://novotempo.com/tv/grade/";
		        break;	        
		    case "chile":
		        $source = "src=http%3A%2F%2Fstream.novotempo.com%3A1935%2Ftv%2Fsmil%3Atvnuevotiempo.smil%2Fmanifest.f4m&loop=true&autoPlay=true&streamType=live";
		        $link_programacao = "http://novotempo.com/tv/grade/";
		        break;
		   	default:
		   		$source = "src=http%3A%2F%2Fstream.novotempo.com%3A1935%2Ftv%2Fsmil%3Atvnovotempo.smil%2Fmanifest.f4m&loop=true&autoPlay=true&streamType=live";
	 			$link_programacao = "http://novotempo.com/tv/grade/";
	 			break;
		}

	?>
		<a class="logo" href="http://novotempo.com" target="_blank" alt="<?php echo $titulo; ?>" title="<?php echo $titulo; ?>"></a>
		<div class="player">

		<?php 		
			$option = $_GET["tv"];
			if ($_GET["lang"] == "pt_BR"){
		?>
				<div class="infos">
					<select class="cs-select cs-skin-border">
						<option value="portugues" 	data-link="portugues"	<?php if ($option == "portugues") echo "selected"; 	?>>Português</option>
						<option value="espanhol" 	data-link="espanhol"	<?php if ($option == "espanhol") echo "selected"; 	?>>Espanhol</option>
						<option value="chile" 		data-link="chile"		<?php if ($option == "chile") echo "selected"; 		?>>Chile</option>

					</select>	
				
		<?php 		
		} else {
		?>
				<div class="infos">
					<select class="cs-select cs-skin-border">
						<option value="portugues" 	data-link="portugues"	<?php if ($option == "portugues") echo "selected"; 	?>>Portugués</option>
						<option value="espanhol" 	data-link="espanhol"	<?php if ($option == "espanhol") echo "selected"; 	?>>Español</option>
						<option value="chile" 		data-link="chile"		<?php if ($option == "chile") echo "selected"; 		?>>Chile</option>

					</select>	
				

		<?php } ?>

			<a href="<?php echo $link_programacao; ?>" target="_blank" alt="<?php echo $programacao; ?>" title="<?php echo $programacao; ?>" class="veja_mais" id="btn_veja_mais"><?php echo $programacao; ?></a>
			</div>

 			<object>
				<param name="movie" value="http://fpdownload.adobe.com/strobe/FlashMediaPlayback_101.swf">
				<param name="flashvars" value="<?php echo $source; ?>">
				<param name="allowFullScreen" value="true">
				<param name="allowscriptaccess" value="always">
				<embed src="http://fpdownload.adobe.com/strobe/FlashMediaPlayback_101.swf" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="555" height="390" flashvars="<?php echo $source; ?>">
			</object> 
		</div>
	</div>

	<script src="<?php echo $_GET["dir"]; ?>/static/lib/classie.js"></script>
	<script src="<?php echo $_GET["dir"]; ?>/static/lib/selectFx.js"></script>

	<script>
		(function() {
			[].slice.call( document.querySelectorAll( 'select.cs-select' ) ).forEach( function(el) {	
				var fx = new SelectFx(el);
				fx._changeOption(el);
			} );
		})();
	</script>
	
</body>
</html>
