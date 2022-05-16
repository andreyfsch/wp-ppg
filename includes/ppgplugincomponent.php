<?php

define('PLUGIN__FILE__', __FILE__);
define('PLUGIN_BASE', plugin_basename(PLUGIN__FILE__));
define('PLUGIN_PATH', plugin_dir_path(PLUGIN__FILE__));
define('PLUGIN_URL', plugins_url('/', PLUGIN__FILE__));

abstract class PPGPluginComponent
{
    const URL_BASE = PLUGIN_URL . '../public/';

    private const WP_SCRIPT_CHECKS = ['enqueued', 'registered', 'queue', 'to_do', 'done'];

    abstract public function parseJSON($bodyItem);
    abstract public function nomeJS();
    abstract public function nomeCSS();
    abstract public function shortCodes();
    abstract public function renderLayout();

    public function verificarScriptsPosts()
    {
        global $posts;
        $pattern = get_shortcode_regex(); 
        preg_match('/'.$pattern.'/s', $posts[0]->post_content, $corresp);
        if (is_array($corresp) && in_array($corresp[2], $this->shortCodes())) { 
            if (!wp_script_is('materialize-javascript', 'enqueued')){
                wp_enqueue_script('materialize-javascript');
                echo "<br>enqueuei materialize-javascript";
            }
            if (!wp_style_is('materialize-css', 'enqueued')){
                wp_enqueue_style('materialize-css');
                echo "<br>enqueuei materialize-css";
            }
            if (!wp_script_is($this->nomeJS(), 'enqueued')){
                wp_enqueue_script($this->nomeJS());
                echo "<br>enqueuei JS: ".$this->nomeJS();
            }
            if (!wp_style_is($this->nomeCSS(), 'enqueued')){
                wp_enqueue_style($this->nomeCSS());
                echo "<br>enqueuei CSS: ".$this->nomeCSS();
            }
        }
    }

    public function __construct($json, $chaveSel = 'data')
    {
        add_action('template_redirect', array($this, 'verificarScriptsPosts'));
        $this->ajustaScriptsBase();
        $this->insereScripts();
        $this->iterarJSON($json, $chaveSel);
        try {
            $this->insereShortCodes();
        } catch (Throwable $e) {
            echo 'Erro: ' . $e->getMessage();
        }
    }

    private function insereShortCodes()
    {
        if (is_string($this->shortCodes())) {
            add_shortcode($this->shortCodes(), array($this, 'renderLayout'));
        } else {
            if (!in_array('renderLayout', $this->shortCodes())) {
                throw new Exception('renderLayout deve ser atribuido a um short-code obrigatoriamente.');
            }
            foreach ($this->shortCodes() as $nome => $metodo) {
                add_shortcode($nome, array($this, $metodo));
            }
        }
    }

    private function insereScripts()
    {
        add_action('wp_enqueue_scripts', array($this, 'insereCSS'));
        echo "<br>botei a action de insereCSS em wp_enqueue_scripts";

        add_action('wp_enqueue_scripts', array($this, 'insereJS'));
        echo "<br>botei a action de insereJS em wp_enqueue_scripts";
    }

    public function insereJS()
    {
        $caminho = self::URL_BASE . 'js/';
        $caminho .= $this->nomeJS() . '.js';
        wp_register_script(
            $this->nomeJS(),
            $caminho,
            array('materialize-javascript'),
            null,
            true
        );
        echo "<br>insereJS - registrei JS: " . $this->nomeJS() . " de " . $caminho;
    }

    public function insereCSS()
    {
        $caminho = self::URL_BASE . 'css/';
        $caminho .= $this->nomeCSS() . '.css';
        wp_register_style(
            $this->nomeCSS(),
            $caminho
        );
        echo "<br>insereCSS - registrei CSS: " . $this->nomeCSS() . " de " . $caminho;
    }

    private function existeScriptWP($nome, $style = false)
    {
        $existe = false;
        foreach (self::WP_SCRIPT_CHECKS as $check) {
            if ($style) {
                if (wp_style_is($nome, $check)) {
                    $existe = true;
                }
            } else {
                if (wp_script_is($nome, $check)) {
                    $existe = true;
                }
            }
        }

        return $existe;
    }

    public function enqueueMaterializejs()
    {
        wp_register_script(
            'materialize-javascript',
            self::URL_BASE . 'js/materialize.min.js',
            [],
            null,
            true
        );
        echo "<br>enqueueMaterializejs - registrei materialize-javascript de " . self::URL_BASE . 'js/materialize.min.js';
    }

    public function enqueueMaterializecss()
    {
        wp_register_style(
            'materialize-css',
            self::URL_BASE . 'css/materialize.min.css'
        );
        echo "<br>enqueueMaterializecss - registrei materialize-css de " . self::URL_BASE . 'css/materialize.min.css';
    }

    private function ajustaScriptsBase()
    {
        if (!$this->existeScriptWP('materialize-javascript')) {
            add_action('wp_enqueue_scripts', array($this, 'enqueueMaterializejs'));
            echo "<br>botei a action de enqueueMaterializejs em wp_enqueue_scripts";
        }
        if (!$this->existeScriptWP('materialize-css', true)) {
            add_action('wp_enqueue_scripts', array($this, 'enqueueMaterializecss'));
            echo "<br>botei a action de enqueueMaterializecss em wp_enqueue_scripts";
        }
    }

    private function iterarJSON($json, $chaveSel)
    {
        $response = json_decode($json, true);
        foreach ($response as $chave => $item) {
            if ($chave == $chaveSel) {
                $this->parseJSON($item);
            }
        }
    }
}
