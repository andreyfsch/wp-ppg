<?php

require_once 'ppgplugincomponent.php';

class CursosConceitosCAPES extends PPGPluginComponent
{
    private $dadosPPG;
    private $cursos;
    private $maiorConceito;
    private $coresCards;

    public function nomeJS()
    {
        return 'cursos-conceitos';
    }

    public function nomeCSS()
    {
        return 'cursos-conceitos';
    }

    public function shortCodes()
    {
        return [
            'cursos-conceitos-capes' => 'renderLayout',
            'badge-conceito-capes' => 'renderBadge'
        ];
    }

    public function renderLayout($badge = false)
    {
        $HTMLfinal = '<div class="materialize-iso">
            <div class="row white-text">
            <div class="card cyan darken-4">';
        if ($badge) {
            $HTMLfinal .= $this->renderBadge($this->maiorConceito);
        } else {

            $HTMLfinal .= $this->desenhaDadosPrograma();

            $HTMLfinal .= $this->desenhaAbasCursos();

            $HTMLfinal .= $this->desenhaConteudoCursos();
        }

        $HTMLfinal .= '</div></div></div>';

        print($HTMLfinal);
    }

    public function renderBadge()
    {
        $this->renderLayout(true);
    }

    public function __construct($json)
    {
        parent::__construct($json);
        $this->maiorConceitoCapes();
        $this->setCoresCards();
    }

    public function parseJSON($bodyItem)
    {
        $this->cursos = [];
        foreach ($bodyItem['items'] as $itemData) {
            $dadosPrograma = array_slice($itemData, 0, 11, true);
            if (empty($this->dadosPPG)) {
                $endereco = $itemData['Logradouro'] . ', ' . $itemData['NrLogradouro'];
                $endereco .= ' ' . $itemData['ComplementoLogradouro'] . ' CEP: ' . $itemData['CEP'];
                $endereco .= ' ' . $itemData['Cidade'] . ', ' . $itemData['UF'];

                $this->dadosPPG['Endereço'] = $endereco;

                $this->dadosPPG = array_merge($this->dadosPPG, $dadosPrograma);
            }
            $dadosCurso = array_slice($itemData, 11, null, true);
            if (!array_key_exists($itemData['IdCurso'], $this->cursos)) {
                $this->cursos[$itemData['NomeNivelCursoPG']] = $dadosCurso;
            }
        }
    }

    public function setCoresCards($cores = null)
    {
        $this->coresCards = is_null($cores) ? ['blue darken-2', 'cyan accent-4', 'purple darken-4'] : $cores;
    }

    private function maiorConceitoCapes()
    {
        $maiorConceito = 0;
        foreach ($this->cursos as $nivel => $dadosCurso) {
            if ($dadosCurso['ConceitoCAPES'] > $maiorConceito) {
                $maiorConceito = $dadosCurso['ConceitoCAPES'];
            }
        }

        $this->maiorConceito = $maiorConceito;
    }

    private function desenhaBadge($conceitoCAPES)
    {
        return '<div class="clear" id="notaCapes">
        <p class="texto">  
          Conceito<br>
          CAPES
         </p>
         <div class="nota">
             <span class="circInterno">' . $conceitoCAPES . ' </span>
         </div>
         </div>';
    }

    private function desenhaDadosPrograma()
    {
        $card = '<div class="card-content">
        <h4 class="white-text">' . $this->dadosPPG['Programa'] . '</h4>
        <p>Unidade: ' . $this->dadosPPG['Unidade'] . '</p>
        <p>Endereço: ' . $this->dadosPPG['Endereço'] . '</p>
        <p>Contato: ' . $this->dadosPPG['Telefone'];

        $card .= ' <a href="' . $this->dadosPPG['URLWeb'] . '" target="_blank">';

        $card .= $this->dadosPPG['URLWeb'] . '</a>';

        $card .= ' E-mail: ' . $this->dadosPPG['EMail'];

        $card .= '</p></div>';

        return $card;
    }

    private function desenhaAbasCursos()
    {
        $tabs = '<div class="card-tabs">
        <ul id="tabs_cursos" class="tabs tabs-fixed-width tabs-transparent">';

        foreach ($this->cursos as $nomeCurso => $dadosCurso) {
            $tabs .= '<li class="tab"><a href="#curso_' . $dadosCurso['IdCurso'] . '"';

            $tabs .= '>' . $nomeCurso . '</a></li>';
        }

        $tabs .= '</ul>
        </div>';

        return $tabs;
    }

    private function desenhaConteudoCursos()
    {
        $cards = '<div class="card-content" id="card-cursos-content">';

        $cor = 0;
        foreach ($this->cursos as $nomeCurso => $dadosCurso) {
            $cards .= '<div id="curso_' . $dadosCurso['IdCurso'] . '" class="' . $this->coresCards[$cor] . '">';
            $cards .= $this->desenhaBadge($dadosCurso['ConceitoCAPES']);
            $cards .= '<p>Área de Conhecimento: ' . $dadosCurso['NomeAreaConhecimento'] . '</p>';
            $cards .= '<p>Modalidade de Ensino: ' . $dadosCurso['ModalidadeEnsino'] . '</p>';
            $cards .= '<p>Período de avaliação: ' . $dadosCurso['PeriodoAvaliacao'] . '</p>';
            $cards .= '</div>';
            $cor++;
        }

        $cards .= '</div>';

        return $cards;
    }
}
