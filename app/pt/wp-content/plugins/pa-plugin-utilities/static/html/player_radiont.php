<?php
	
	if ($_GET["lang"] == "pt_BR"){
		$titulo = "Rádio Novo Tempo";
		$programacao = "Veja a programação";
	} else {
		$titulo = "Radio Nuevo Tiempo";
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

<body class="radiont">
	
	<div class="<?php echo $_GET["lang"]; ?>">

	<?php 
		
		$source = $_GET["radio"];

		switch ($source) {
		    case "portugues":
		        $source = "src=http%3A%2F%2Fstream.novotempo.com%3A1935%2Fradio%2Fsmil%3Aradionovotempo.smil%2Fmanifest.f4m&amp;loop=true&amp;autoPlay=true&amp;streamType=live&amp;controlBarAutoHide=false";
		        $link_programacao ="http://novotempo.com/radio/grade/";
		        break;
		    case "espanhol":
		        $source = "src=http%3A%2F%2Fstream.novotempo.com%3A1935%2Fradio%2Fsmil%3Aradionuevotiempo.smil%2Fmanifest.f4m&loop=true&autoPlay=true&streamType=live&amp;controlBarAutoHide=false";
		        $link_programacao ="http://novotempo.com/radio/grade/";
		        break;	        
		    case "bolivia":
		        $source = "src=http%3A%2F%2Fstream.novotempo.com%3A1935%2Fradio%2Fsmil%3ArntCochabambaBO.smil%2Fmanifest.f4m&amp;loop=true&amp;autoPlay=true&amp;streamType=live&amp;controlBarAutoHide=false";
		        $link_programacao ="http://novotempo.com/radio/grade/";
		        break;
		    case "equador":
		        $source = "src=http%3A%2F%2Fstream.novotempo.com%3A1935%2Fradio%2Fsmil%3ArntQuitoEC.smil%2Fmanifest.f4m&amp;loop=true&amp;autoPlay=true&amp;streamType=live&amp;controlBarAutoHide=false";
		        $link_programacao ="http://novotempo.com/radio/grade/";
		        break;
		    case "uruguai":
		        $source = "src=http%3A%2F%2Fstream.novotempo.com%3A1935%2Fradio%2Fsmil%3ArntMontevideoUY.smil%2Fmanifest.f4m&loop=true&autoPlay=true&streamType=live";
		   		$link_programacao ="http://novotempo.com/radio/grade/";
		   		break;
		   	default:
		   		$source = "src=http%3A%2F%2Fstream.novotempo.com%3A1935%2Fradio%2Fsmil%3Aradionovotempo.smil%2Fmanifest.f4m&amp;loop=true&amp;autoPlay=true&amp;streamType=live&amp;controlBarAutoHide=false";
	 			$link_programacao ="http://novotempo.com/radio/grade/";
	 			break;
		}

	?>

	 	<a class="logo" href="http://novotempo.com" target="_blank" alt="<?php echo $titulo; ?>" title="<?php echo $titulo; ?>"></a>
		<div class="player">
			<div class="infos">
		<?php 		
			$option = $_GET["radio"];
		if ($_GET["lang"] == "pt_BR"){
		?>
					<select class="cs-select cs-skin-border" >
						<option value="portugues" data-link="portugues" <?php if ($option == "portugues") echo "selected"; 	?>>Português</option>
						<option value="espanhol"  data-link="espanhol" 	<?php if ($option == "espanhol") echo "selected"; 	?>>Espanhol</option>
						<option value="bolivia"   data-link="bolivia"	<?php if ($option == "bolivia") echo "selected"; 	?>>Bolivia</option>
						<option value="equador"   data-link="equador" 	<?php if ($option == "equador") echo "selected"; 	?>>Equador</option>
						<option value="uruguai"   data-link="uruguai" 	<?php if ($option == "uruguai") echo "selected"; 	?>>Urugai</option>
					</select>	
		<?php 		
		} else {
		?>
					<select class="cs-select cs-skin-border">
						<option value="portugues" data-link="portugues"	<?php if ($option == "portugues") echo "selected"; 	?>>Portugués</option>
						<option value="espanhol"  data-link="espanhol"  <?php if ($option == "espanhol") echo "selected"; 	?>>Español</option>
						<option value="bolivia"   data-link="bolivia"	<?php if ($option == "bolivia") echo "selected"; 	?>>Bolivia</option>
						<option value="equador"   data-link="equador"	<?php if ($option == "equador") echo "selected"; 	?>>Ecuador</option>
						<option value="uruguai"   data-link="uruguai"	<?php if ($option == "uruguai") echo "selected"; 	?>>Uruguay</option>
					</select>	
		<?php } ?>
			
			<a href="<?php echo $link_programacao; ?>" target="_blank" alt="<?php echo $programacao; ?>" title="<?php echo $programacao; ?>" class="veja_mais" id="btn_veja_mais"><?php echo $programacao; ?></a>
			</div>

			<div>
			<audio src="<?php echo $source; ?>" width="320" height="240">
			</div>
			<object width="420" height="32">
				<param name="movie" value="http://fpdownload.adobe.com/strobe/FlashMediaPlayback_101.swf">
				<param name="flashvars" value="<?php echo $source; ?>">
				<param name="allowFullScreen" value="true">
				<param name="allowscriptaccess" value="always">
				<embed src="http://fpdownload.adobe.com/strobe/FlashMediaPlayback_101.swf" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="420" height="32" flashvars="<?php echo $source; ?>">
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