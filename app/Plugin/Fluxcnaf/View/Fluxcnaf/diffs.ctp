<style type="text/css" media="all">
	html { font-size: 10px; }
	div.flux_diff tbody th {
		vertical-align: top;
		background-color: #a3c7d9;
		color: black;
	}
</style>


<h1 >Menu </h1>

<a href="#VRSD0301-VRSB0801"> Comparaison entre les flux VRSD0301 et VRSB0801</a> <br>
<a href="#VRSD0301-VRSD0101">Comparaison entre les flux VRSD0301 et VRSD0101</a> <br>
<a href="#VRSD0101-VRSB0801"> Comparaison entre les flux VRSD0101 et VRSB0801 </a> <br>

<h1 id="VRSD0301-VRSB0801">Comparaison entre les flux VRSD0301 et VRSB0801</h1>
<?php
	$container = '';
	foreach( $results[0308] as $key => $result ) {
		$tbody = '';
		$row = 0;

		foreach( $result as $path => $fields ) {
			$row++;
			$fields = array_values( $fields );
			$rowspan = count( $fields );
			$tbody .= "<tr><th rowspan=\"{$rowspan}\">{$row}. {$path}</th><td>{$fields[0]}</td></tr>";
			for( $i = 1 ; $i < $rowspan ; $i++ ) {
				$tbody .= "<tr><td>{$fields[$i]}</td></tr>";
			}
		}

		$title = 'Dans '.implode( ' et pas dans ', explode( '_', $key ) );
		$table = "<div class=\"flux_diff\"><table><thead><tr><th colspan=\"2\">{$title}</th></tr></thead><tbody>{$tbody}</tbody></table></div>";
		$container .= "<td>{$table}</td>";
	}
	echo "<table><tr>{$container}</tr></table>"
?>
<h1 id="VRSD0301-VRSD0101">Comparaison entre les flux VRSD0301 et VRSD0101</h1>
<?php
	$container = '';
	foreach( $results[0301] as $key => $result ) {
		$tbody = '';
		$row = 0;

		foreach( $result as $path => $fields ) {
			$row++;
			$fields = array_values( $fields );
			$rowspan = count( $fields );
			$tbody .= "<tr><th rowspan=\"{$rowspan}\">{$row}. {$path}</th><td>{$fields[0]}</td></tr>";
			for( $i = 1 ; $i < $rowspan ; $i++ ) {
				$tbody .= "<tr><td>{$fields[$i]}</td></tr>";
			}
		}

		$title = 'Dans '.implode( ' et pas dans ', explode( '_', $key ) );
		$table = "<div class=\"flux_diff\"><table><thead><tr><th colspan=\"2\">{$title}</th></tr></thead><tbody>{$tbody}</tbody></table></div>";
		$container .= "<td>{$table}</td>";
	}
	echo "<table><tr>{$container}</tr></table>"
?>

<h1 id="VRSD0101-VRSB0801"> Comparaison entre les flux VRSD0101 et VRSB0801 </h1>
<?php
	$container = '';
	foreach( $results[0108] as $key => $result ) {
		$tbody = '';
		$row = 0;

		foreach( $result as $path => $fields ) {
			$row++;
			$fields = array_values( $fields );
			$rowspan = count( $fields );
			$tbody .= "<tr><th rowspan=\"{$rowspan}\">{$row}. {$path}</th><td>{$fields[0]}</td></tr>";
			for( $i = 1 ; $i < $rowspan ; $i++ ) {
				$tbody .= "<tr><td>{$fields[$i]}</td></tr>";
			}
		}

		$title = 'Dans '.implode( ' et pas dans ', explode( '_', $key ) );
		$table = "<div class=\"flux_diff\"><table><thead><tr><th colspan=\"2\">{$title}</th></tr></thead><tbody>{$tbody}</tbody></table></div>";
		$container .= "<td>{$table}</td>";
	}
	echo "<table><tr>{$container}</tr></table>"
?>