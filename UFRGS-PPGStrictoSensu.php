<?php
/**
 * Plugin Name: UFRGS - PPG Stricto Sensu
* Plugin URI: https://www1.ufrgs.br/catalogoti/servicos/servico?servico=33
* Description: O objetivo deste plugin é permitir que os progamas de pós-graduação da UFRGS 
*              disponibilizem dados públicos de mestrado e doutorado em suas páginas Wordpress.
* Version: 1.0
* Author: CPD-UFRGS
* Author URI: devanir@cpd.ufrgs.br, andrey@cpd.ufgs.br
* */

require 'includes/apiufrgshandler.php';
require 'includes/cursosconceitoscapes.php';

$api = new APIUFRGSHandler();

$cursosConceitos = new CursosConceitosCAPES($api->consome_api('cursos'));
