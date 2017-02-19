<html class="">
<head>
	<meta charset="UTF-8">
	<title>Rsync'ology</title>
	<meta name="robots" content="noindex">
	<link rel=stylesheet type="text/css" href="./style-synchro.css">
</head>

<body>
		<?php
			$header="Aucun transfert en cours...";
			$file='/tmp/synchro';
			$log='/home/@user@/synchro/logs/sending.log';

			if(file_exists($file))
			{
				$header="Transfert en cours";
				
				// On récupère les informations sur les transferts en cours
				$tail=shell_exec('tail -c 28 /home/@user@/synchro/logs/sending.log');
				
				// On lit le fichier pour récupérer le nom du fichier en cours de transfert
				$fp=fopen($log,'r');
				$fichier=fgets($fp,500);
				fclose($fp);

				// On stocke la ligne dans la variable $titre en supprimant les espaces inutiles
				$titre=trim($fichier);
				
				// On récupère les informations sur le fichier en question
				$info=stat($titre);
				
				// On récupère et on adapte la taille du fichier
				$taille=($info[7]/1024)/1024;

				// On nettoie les inputs
				$titre=preg_replace('/^\/.*\//','',$titre);
				$tail=preg_replace('/^[\s]+/','',$tail);
				$info=preg_split('/[\s]+/',$tail);

				// On assigne les valeurs aux variables
				$percentage=$info[0];
				$vitesse=$info[1];
				$duree=$info[2];

				// On adapte l'affichage de la taille
				if($taille>=999) {
					$taille=round(($taille/1024), 2)."Go";
				}
				else {
					$taille=round($taille, 2)."Mo";
				}
			}
		?>

<section class="box widget synchro">
	<header class="header">
		<h2><?php echo $header ?></h2>
	</header>
	<div id="synchro">
		<?php echo $titre ?>
		<div class="ui-progress-bar ui-container transition" id="progress_bar">
			<div class="ui-progress" style="width: <?php echo $percentage ?>">
				<span class="ui-label"><?php echo $percentage ?></span>
			</div>
		</div>
		<div id="infosync">
			Taille : <?php echo $taille ?> | Vitesse : <?php echo $vitesse ?><br>
			Temps restant : <?php echo $duree ?>
		</div><br>
		<a href="#" class="info"><span>
		<?php
			$list='/home/@user@/synchro/logs/liste_fichiers';
			if(file_exists($file))
			{
				$nbcont=0;
				$fp=fopen($list,'r');
				while(!feof($fp))
				{
					$line=fgets($fp,255);
					if (preg_match("/\:+.{0,}/", $line, $matches3))
					{
						$eta = $matches3[0];
						$eta = str_replace(':','',$eta);
						$val = explode('/', $eta);
						$count = count($val);
						$val = $val[$count - 1];
						if(!preg_match('/\.srt$/',$val))
						{
							echo $val;
							echo "<br>";
							$nbcont++;
						}
					}
				}
				fclose($fp);
			}
		?>	
		</span><?php echo $nbcont ?> fichiers restant(s)</a><br><br>
	</div>
</section>	
	
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>

<!-- Actualisation de la synchro toutes les secondes -->
<script type="text/javascript">
	var auto_refresh = setInterval(
	function ()
	{
		$('#synchro').load('./synchro.php #synchro');
	}, 1000); // refresh every 10000 milliseconds
</script>

</body>
</html>