<?php
require("includes/config.inc.php");
require("includes/common.inc.php");
require("includes/db.inc.php");

$conn = dbConnect();

function zeigeForum(?int $fid=null):void {
	global $conn;
	
	if(is_null($fid)) {
		$where = "
			WHERE(
				tbl_eintraege.FIDEintrag IS NULL
			)
		";
	}
	else {
		$where = "
			WHERE(
				tbl_eintraege.FIDEintrag=" . $fid . "
			)
		";
	}
	
	$sql = "
		SELECT
			tbl_eintraege.Eintrag,
			tbl_eintraege.Eintragezeitpunkt,
			tbl_eintraege.IDEintrag,
			tbl_user.Vorname,
			tbl_user.Nachname
		FROM tbl_eintraege
		LEFT JOIN tbl_user ON tbl_user.IDUser=tbl_eintraege.FIDUser
	" . $where . "
		ORDER BY tbl_eintraege.Eintragezeitpunkt ASC
	";
	
	$eintraege = dbQuery($conn,$sql);
	echo('<ul>');
	while($eintrag = $eintraege->fetch_object()) {
		if(is_null($eintrag->Vorname) && is_null($eintrag->Nachname)) {
			$name = "(Anonymous)";
		}
		else {
			$name = $eintrag->Vorname . ' ' . $eintrag->Nachname;
		}
		echo('
			<li>
				' . $name . ' schrieb am ' . date("d.m.Y",strtotime($eintrag->Eintragezeitpunkt)) . ' um ' . date("H:i",strtotime($eintrag->Eintragezeitpunkt)) . ' Uhr:
				<div>' . $eintrag->Eintrag . '</div>
		');
		
		// ---- Rekursion: ----
		zeigeForum($eintrag->IDEintrag);
		// --------------------
		
		echo('
			</li>
		');
	}
	echo('</ul>');
}
?>
<!doctype html>
<html lang="de">
	<head>
		<title>Newsforum</title>
		<meta charset="utf-8">
	</head>
	<body>
		<h1>Newsforum</h1>
		<nav>
			<ul>
				<li><a href="eintraege.php">Einträge im Newsforum</a></li>
				<li><a href="eintraege_user.php">Einträge von Usern</a></li>
				<li><a href="eintragssuche.php">Suche nach Text</a></li>
			</ul>
		</nav>
		<?php
		zeigeForum();
		?>
	</body>
</html>