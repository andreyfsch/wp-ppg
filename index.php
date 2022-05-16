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
function cursos_e_conceitos_capes() {

    $response = wp_remote_get('https://desenvolvimento.dsi/HomeSVN/thiago/api/api/web/v2/pos-graduacao/programa/cursos?IdPrograma=1433',
            array(
                'sslverify' => false,
            )
    );
    $body = wp_remote_retrieve_body($response);
    $ar_valores = json_decode($body, true);
    echo '<div>';
    if ($ar_valores) {
        foreach ($ar_valores as $key => $value) {
            echo '<br><br>' . $value . '<br><br>';
        }
    } else {
        echo 'Erro ao acessar API-UFRGS!!!!<br><br><br>';
    }
    echo '</div>';
}

add_shortcode('cursos_e_conceitos_capes', 'cursos_e_conceitos_capes');

function linhas_de_pesquisa() {

    $response = wp_remote_get('https://desenvolvimento.dsi/HomeSVN/thiago/api/api/web/v2/pos-graduacao/programa/pesquisa?IdPrograma=1433',
            array(
                'sslverify' => false,
            )
    );
    $body = wp_remote_retrieve_body($response);
    $ar_valores = json_decode($body, true);
    echo '<div>';
    if ($ar_valores) {
        foreach ($ar_valores as $key => $value) {
            echo '<br><br>' . $value . '<br><br>';
        }
    } else {
        echo 'Erro ao acessar API-UFRGS!!!!<br><br><br>';
    }
    echo '</div>';
}

add_shortcode('linhas_de_pesquisa', 'linhas_de_pesquisa');

function disciplinas_do_programa() {

    $response = wp_remote_get('https://desenvolvimento.dsi/HomeSVN/thiago/api/api/web/v2/pos-graduacao/programa/disciplinas?IdPrograma=1433',
            array(
                'sslverify' => false,
            )
    );
    $body = wp_remote_retrieve_body($response);
    $ar_valores = json_decode($body, true);
    echo '<div>';
    if ($ar_valores) {
        foreach ($ar_valores as $key => $value) {
            echo '<br><br>' . $value . '<br><br>';
        }
    } else {
        echo 'Erro ao acessar API-UFRGS!!!!<br><br><br>';
    }
    echo '</div>';
}

add_shortcode('disciplinas_do_programa', 'disciplinas_do_programa');


function teses_e_dissertacoes() {

    $response = wp_remote_get('https://desenvolvimento.dsi/HomeSVN/thiago/api/api/web/v2/pos-graduacao/programa/teses?IdPrograma=1433',
            array(
                'sslverify' => false,
            )
    );
    $body = wp_remote_retrieve_body($response);
    $ar_valores = json_decode($body, true);
    echo '<div>';
    if ($ar_valores) {
        foreach ($ar_valores as $key => $value) {
            echo '<br><br>' . $value . '<br><br>';
        }
    } else {
        echo 'Erro ao acessar API-UFRGS!!!!<br><br><br>';
    }
    echo '</div>';
}

add_shortcode('teses_e_dissertacoes', 'teses_e_dissertacoes');

function docentes_ativos_do_programa() {

    $response = wp_remote_get('https://desenvolvimento.dsi/HomeSVN/thiago/api/api/web/v2/pos-graduacao/programa/docentes?IdPrograma=1433',
            array(
                'sslverify' => false,
            )
    );
    $body = wp_remote_retrieve_body($response);
    $ar_valores = json_decode($body, true);
    echo '<div>';
    if ($ar_valores) {
        foreach ($ar_valores as $key => $value) {
            echo '<br><br>' . $value . '<br><br>';
        }
    } else {
        echo 'Erro ao acessar API-UFRGS!!!!<br><br><br>';
    }
    echo '</div>';
}

add_shortcode('docentes_ativos_do_programa', 'docentes_ativos_do_programa'); 

function discentes_ativos_do_programa() {

    $response = wp_remote_get('https://desenvolvimento.dsi/HomeSVN/thiago/api/api/web/v2/pos-graduacao/programa/discentes?IdPrograma=1433',
            array(
                'sslverify' => false,
            )
    );
    $body = wp_remote_retrieve_body($response);
    $ar_valores = json_decode($body, true);
    echo '<div>';
    if ($ar_valores) {
        foreach ($ar_valores as $key => $value) {
            echo '<br><br>' . $value . '<br><br>';
        }
    } else {
        echo 'Erro ao acessar API-UFRGS!!!!<br><br><br>';
    }
    echo '</div>';
}

add_shortcode('discentes_ativos_do_programa', 'discentes_ativos_do_programa');

define( 'PLUGIN__FILE__', __FILE__ );
define( 'PLUGIN_BASE', plugin_basename( PLUGIN__FILE__ ) );
define( 'PLUGIN_PATH', plugin_dir_path( PLUGIN__FILE__ ) );
define( 'PLUGIN_URL', plugins_url( '/', PLUGIN__FILE__ ) );


function add_css_file() {
  wp_enqueue_style('materialize-css', PLUGIN_URL .'includes/css/materialize.min.css');
  wp_enqueue_style('plugin-css', PLUGIN_URL .'includes/css/estilo_plugin.css');
  wp_enqueue_style('material-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons');
}

function add_js_file() {
  wp_enqueue_script('materialize-javascript', PLUGIN_URL .'includes/js/materialize.min.js', null, null, true);
  wp_enqueue_script('plugin-javascript', PLUGIN_URL .'includes/js/plugin.js', 'materialize-javascript', null, true);
}


add_action('wp_enqueue_scripts', 'add_css_file');
add_action('wp_enqueue_scripts', 'add_js_file');


require 'apiufrgshandler.php';

$api = new APIUFRGSHandler();


require 'cursosconceitoscapes.php';

$cursosConceitos = new CursosConceitosCAPES($api->consome_api('cursos'));

function cursosConceitosCAPES() {
    global $cursosConceitos;
    $cursosConceitos->renderLayout();
}

add_shortcode('cursos-conceitos-capes', 'cursosConceitosCAPES');

function badgeConceitoCAPES() {
    global $cursosConceitos;
    $cursosConceitos->renderLayout(true);
}

add_shortcode('badge-conceito-capes', 'badgeConceitoCAPES');

require 'linhaspesquisa.php';

function linhasPesquisa()
{
	global $api;
    $linhasPesquisa = new LinhasPesquisa($api->consome_api('pesquisa'));
    $linhasPesquisa->renderLayout();
}

add_shortcode('linhas-pesquisa', 'linhasPesquisa');

require 'disciplinas.php';

function disciplinas()
{
	global $api;
    $disciplinas = new Disciplinas($api->consome_api('disciplinas'));
    $disciplinas->renderLayout();
}

add_shortcode('disciplinas-programa', 'disciplinas');