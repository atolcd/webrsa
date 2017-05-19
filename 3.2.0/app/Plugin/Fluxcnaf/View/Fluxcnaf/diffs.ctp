<style type="text/css" media="all">
	html { font-size: 10px; }
	div.flux_diff tbody th {
		vertical-align: top;
		background-color: #a3c7d9;
		color: black;
	}
</style>
<h1>Comparaison entre les flux VRSD0301 et VRSD0101</h1>
<?php
	$container = '';
	foreach( $results as $key => $result ) {
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