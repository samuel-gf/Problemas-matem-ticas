<?php
# Error: Faltan argumentos
if ($argc < 2) exit(1);

# Programa principal
$s = file_get_contents($argv[1]);
$arr_parrafo = preg_split("/\n\n+/", $s);

for($i=0; $i<sizeof($arr_parrafo); $i++){
	$arr_parrafo[$i] = str_replace("%", "\%", $arr_parrafo[$i]);
	$arr_parrafo[$i] = str_replace("€", "\\euro{}", $arr_parrafo[$i]);
	$arr_parrafo[$i] = str_replace("\t", "  ", $arr_parrafo[$i]);
	$arr_parrafo[$i] = preg_replace("/\*{2}([^[:punct:]]*)\*{2}/", '\\textbf{$1}', $arr_parrafo[$i]);
	$arr_parrafo[$i] = preg_replace("/\*{1}([^[:punct:]]*)\*{1}/", '\\textit{$1}', $arr_parrafo[$i]);

	# Si el primer carácter es #, se trata de una sección
	if (substr($arr_parrafo[$i], 0, 2) == "# "){
		echo("\section{". substr($arr_parrafo[$i],2). "}\n\n");
		continue;
	} 


	# Es el enunciado de un problema
	if (strlen($arr_parrafo[$i]) == 0)	continue;	# Si está vacío sálta
	echo("\\begin{problema}\n{\n");
	# El modo de funcionamiento
	$enumerate = false;
	$tabular = false;

	$arr_linea = explode("\n", $arr_parrafo[$i]);
	for ($j=0; $j<sizeof($arr_linea); $j++){
		# Si comienza por | es una tabla
		if (substr($arr_linea[$j], 0, 1) == "|"){
			if($tabular == false){
				# Es la primera línea de la tabla
				$tabular = true;
				echo("\\begin{center}\n");
				$num_columnas = substr_count($arr_linea[$j], "|") - 1;
				echo("\begin{tabular}{  l ");
				echo(str_repeat(" c ", $num_columnas-1)."}\n");
				echo("\\hline\n");
				$arr_cabecera = explode("|", $arr_linea[$j]);
				for ($k=1; $k<sizeof($arr_cabecera)-2; $k++){
					echo($arr_cabecera[$k]." & ");
				}
				echo($arr_cabecera[sizeof($arr_cabecera)-2] . "\\\\\n");
				continue;
			} else {
				# No es la primera línea de la tabla (pero es una tabla)
				if (preg_match("/^\|-[\|-]*$/", $arr_linea[$j])==1) {
					# Es una línea \hline
					echo ("\\hline\n");
					continue;
				} 
				$arr_fila = explode("|", $arr_linea[$j]);
				for($k=1; $k<sizeof($arr_fila)-2; $k++){
					echo($arr_fila[$k]." & ");
				}
				echo($arr_fila[sizeof($arr_fila)-2] . "\\\\\n");
				continue;
			}
		}
		# Antes era una tabla, pero ya no lo es
		if ($tabular) {
			echo("\\hline\n");
			echo("\\end{tabular}\n");
			echo("\\end{center}\n");
			$tabular = false;
		}


		# Es item de lista
		if (preg_match("/^[a-zA-Z]\. /",$arr_linea[$j])==1) {
			if (!$enumerate){	
				$enumerate = true;
				echo "\begin{enumerate}[label=\alph*)]\n";
			}
			echo "  \item ".substr($arr_linea[$j],2)."\n";
			continue;
		}
		# Es continuidad de item de lista
		if (preg_match("/^  /", $arr_linea[$j])==1) {
			echo $arr_linea[$j]."\n";
			continue;
		}
		# Si hemos llegado hasta aquí no es item ni continuidad de item
		if ($enumerate) {
			$enumerate = false;
			echo "\\end{enumerate}\n";
		}


		# Solo escribir la línea, sin más
		echo $arr_linea[$j]."\n";
	}
	# Cerramos el problema
	if ($enumerate) {
		echo "\\end{enumerate}\n";
	}
	echo("}{}\\end{problema}\n\n");

}	
