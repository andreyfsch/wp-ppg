<?php

abstract class ColecaoPaginavel
{
    abstract public function getCabecalho();
    abstract public function setCabecalho($cabecalho);
    abstract public function getItems();
    abstract public function setItems($items);
    abstract public function executaAcao();
    abstract public function setExecutaAcao($executaAcao);
    abstract public function getIconesAcoes();
    abstract public function setIconesAcoes($iconesAcoes);
    abstract public function getDados();
    abstract public function setDados($dados);
    abstract public function getClasse();
    abstract public function setClasse($classe);
    abstract public function getMinLinhas();
    abstract public function setMinLinhas($min);
    abstract public function geraHTML();
    abstract protected function desenhaCabecalho();
    abstract protected function desenhaItem($item);
    abstract protected function abreTag();
    abstract protected function fechaTag();
    abstract protected function desenhaDadosItem($item);

    public final function desenhaColecao() 
    {
        print $this->geraHTML();
    }

}

class ColecaoPaginavelMaterialize extends ColecaoPaginavel
{
    private $cabecalho;
    private $items;
    private $executaAcao;
    private $iconesAcoes;
    private $idsAcoes;
    private $dados;
    private $classe;
    private $minLinhas;
	private $classAcao;
	private $manterLinhas;

    public function getCabecalho()
    {
        return $this->cabecalho;
    }

    public function setCabecalho($cabecalho)
    {
        $this->cabecalho = $cabecalho;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function setItems($items)
    {
        $this->items = $items;
    }

    public function executaAcao()
    {
        return $this->executaAcao;
    }

    public function setExecutaAcao($executaAcao)
    {
        $this->executaAcao = $executaAcao;
    }

    public function getIconesAcoes()
    {
        return $this->iconesAcoes;
    }

    public function setIconesAcoes($iconesAcoes)
    {
        $this->iconesAcoes = $iconesAcoes;
    }

    public function getIdsAcoes()
    {
        return $this->idsAcoes;
    }

    public function setIdsAcoes($idsAcoes)
    {
        $this->idsAcoes = $idsAcoes;
    }

    public function getDados()
    {
        return $this->dados;
    }

    public function setDados($dados)
    {
        $this->dados = $dados;
    }

    public function getClasse()
    {
        return $this->classe;
    }

    public function setClasse($classe)
    {
        $this->classe = $classe;
    }

    public function getMinLinhas()
    {
        return $this->minLinhas;
    }

    public function setMinLinhas($min)
    {
        $this->minLinhas = $min;
    }
	
	public function getClassAcao()
    {
        return $this->classAcao;
    }

    public function setClassAcao($class)
    {
        $this->classAcao = $class;
    }
	
	public function manterLinhas()
    {
        return $this->manterLinhas;
    }

    public function setManterLinhas($bool)
    {
        $this->manterLinhas = $bool;
    }

    protected function desenhaCabecalho()
    {
        return;
    }

    protected function desenhaLinha()
    {
        return;
    }

    protected function desenhaItem($item)
    {
        return;
    }

    protected function abreTag()
    {
        return;
    }

    protected function fechaTag()
    {
        return;
    }

    public final function geraHTML()
    {
        $html = '';

        $html = $this->abreTag();

        if (!is_null($this->getCabecalho())) {
            $html .= $this->desenhaCabecalho();
        }

        if (is_null($this->getMinLinhas())
        || ($this->items 
        && count($this->getItems()) >= $this->getMinLinhas())) {
            foreach ($this->getItems() as $item) {
                $html .= $this->desenhaItem($item);
            }
        } else {
            for ($item = 0; $item < $this->getMinLinhas(); $item++) {
                if ($this->getItems()[$item]) {
                    $html .= $this->desenhaItem($this->getItems()[$item]);
                } elseif ($this->manterLinhas()) {
                    $html .= $this->desenhaItem(null);
                }
            }
        }

        $html .= $this->FechaTag();

        return $html;

    }

    protected final function desenhaDadosItem($item) {
        $dados = '';
        if ($this->getDados()) {
            foreach ($this->getDados() as $nomeDado => $chaveDado) {
                if ($nomeDado != 'href') {
                    $dados .= ' data-'.$nomeDado.'="'.$item[$chaveDado].'" ';
                } else {
                    $dados .= $nomeDado.'="#'.$this->getIdsAcoes()[$chaveDado].'_'.$item[$chaveDado].'" ';
                }
            }
        }

        return $dados;
    }

    public function __construct($items, $classe, $cabecalho,
     $executaAcao, $iconesAcoes, $idsAcoes, $dados, $minLinhas,
	 $classAcao, $manterLinhas)
    {
        $this->setItems($items);
        $this->setClasse($classe);
        $this->setCabecalho($cabecalho);
        $this->setExecutaAcao($executaAcao);
        $this->setIconesAcoes($iconesAcoes);
        $this->setIdsAcoes($idsAcoes);
        $this->setDados($dados);
        $this->setMinLinhas($minLinhas);
		$this->setClassAcao($classAcao);
		$this->setManterLinhas($manterLinhas);
    }
    
}

class TabelaPaginavel extends ColecaoPaginavelMaterialize
{
    const opcoesPadrao = ['classe' => 'striped',
    'cabecalho' => null, 'executaAcao' => false,
    'iconesAcoes' => ['search'],
    'idsAcoes' => ['modal'],
    'dados' => null, 
	'minLinhas' => 5,
	'classAcao' => null,
	'manterLinhas' => true];

    protected function abreTag()
    {
        return '<table class="'.$this->getClasse().' white-text">';
    }

    protected function fechaTag()
    {
        return '</table>';
    }

    protected function desenhaCabecalho()
    {
        $thead = '<thead><tr>';
        foreach ($this->getCabecalho() as $titulo_coluna => $width) {
            if (!is_null($width)) {
                $thead .= '<th style="width:'.$width.'%">'.$titulo_coluna.'</th>';
            } else {
                $thead .= '<th>'.$titulo_coluna.'</th>';
            }
        }
        if ($this->executaAcao()) {
            foreach ($this->getIconesAcoes() as $icone => $width) {
                if (!is_null($width)) {
                    $thead .= '<th style="width:'.$width.'%"><wbr></th>';
                } else {
                    $thead .= '<th><wbr></th>';
                }
            }
        }
        $thead .= '</tr></thead>';

        return $thead;
    }

    protected function desenhaItem($item)
    {
        $html = '<tr'.$this->desenhaDadosItem($item).'>';

        if (is_array($item)) {
            if ($this->getCabecalho()) {
                foreach ($this->getCabecalho() as $tituloColuna => $width) {
                    $html .= '<td>'.$item[$tituloColuna].'</td>';
                }
            } else {
                foreach ($item as $coluna) {
                    $html .= '<td>'.$coluna.'</td>';
                }
            }
        } else {
            if (is_null($item)) {
                $html .= '<td colspan="'.count($this->getCabecalho()).'"><wbr></td>';
            } else {
                $html .= '<td>'.$item.'</td>';
            }
        }

        if ($this->executaAcao()) {
            foreach ($this->getIconesAcoes() as $icone => $width) {
                $html .= '<td><a href="#!"><i class="material-icons">'.$icone.'</i></a></td>';
            }
        }

        $html .= '</tr>';

        return $html;
    }

    public function __construct($items, $classe='striped',
    $cabecalho=null, $executaAcao=false,
    $iconesAcoes=['search'=>5], $idsAcoes=null,
    $dados=null, $minLinhas=5, $classAcao=null,
	$manterLinhas=true)
    {
        parent::__construct($items, $classe, $cabecalho,
        $executaAcao, $iconesAcoes, $idsAcoes, $dados, $minLinhas, $classAcao, $manterLinhas);
    }

}

class ListaPaginavel extends ColecaoPaginavelMaterialize
{
    const opcoesPadrao = ['classe' => null,
    'cabecalho' => null, 'executaAcao' => false,
    'iconesAcoes' => ['search'],
    'idsAcoes' => null,
    'dados' => null,
	'minLinhas' => 5,
	'classAcao' => null,
	'manterLinhas' => true];
	

    protected function abreTag()
    {
        $header = $this->getCabecalho() ? 'with-header ' : null;

        if ($this->executaAcao()) {
            $tag = '<div';
        } else {
            $tag = '<ul';
        }
        $tag .= ' class="collection '.$header.$this->getClasse().' white-text">';

        return $tag;
    }

    protected function fechaTag()
    {
        if ($this->executaAcao()) {
            $tag = '</div>';
        } else {
            $tag = '</ul>';
        }
        return $tag;
    }

    protected function desenhaCabecalho()
    {
		$tag = $this->executaAcao() ? 'div' : 'li';
        return '<'.$tag.' class="collection-header"><h6>'.$this->getCabecalho().'</h6></'.$tag.'>';
    }

    protected function desenhaItem($item)
    {
        if (is_null($item) && $this->manterLinhas()) {
			$tag = $this->executaAcao() ? 'div' : 'li';
            $html = '<'.$tag.' class="collection-item"><wbr></'.$tag.'>';
        } else {
            if (is_array($item)) {
                if ($this->executaAcao()) {
                    $classe = in_array('modal', $this->getIdsAcoes()) ? 'collection-item modal-trigger' : 'collection-item';
                    $html = '<a class="'.$classe.'"'.$this->desenhaDadosItem($item).'>';
                    $html .= $item[0].'<i class="material-icons secondary-content '.$this->getClassAcao().'">';
                    $html .= $this->getIconesAcoes()[0].'</i></a>';
                } else {
                    $html = '<li class="collection-item"'.$this->desenhaDadosItem($item).'>'.$item[0].'</li>';
                }
            } else {
                $html = '<li class="collection-item">'.$item.'</li>';
            }
        }

        return $html;
        
    }

    public function __construct($items, $classe=null,
     $cabecalho=null, $executaAcao=false,
     $iconesAcoes=['search'=>5], $idsAcoes=null,
	 $dados=null, $minLinhas=5, $classAcao=null,
	 $manterLinhas=true)
    {
        parent::__construct($items, $classe, $cabecalho,
        $executaAcao, $iconesAcoes, $idsAcoes, $dados, $minLinhas, $classAcao, $manterLinhas);
    }
}


class PaginacaoMaterialize
{
    private $idAppend;
    private $numItensPag;
    private $items;
    private $totalItems;
    private $paginavel;
    private $numPaginas;
	private $classAcao;
	private $manterLinhas;

    public function __construct($items, $tipo='lista', $idAppend=null,
    $opcoes=null, $numItensPag=5, $classAcao=null, $manterLinhas=true)
    {
        
        $this->idAppend = $idAppend ? '_'.$idAppend : null;

        $this->numItensPag = $numItensPag;
        
        $this->items = $items;

        $this->totalItems = count($items);

        $this->numPaginas = intval(ceil($this->totalItems/$this->numItensPag));
		
		$this->classAcao = $classAcao;
		
		$this->manterLinhas = $manterLinhas;

        if ($tipo == 'lista') {
            if ($opcoes) {
                $opcoes = array_replace(ListaPaginavel::opcoesPadrao, $opcoes);
                $paginavel = new ListaPaginavel($items, $opcoes['classe'],
                $opcoes['cabecalho'], $opcoes['executaAcao'],
                $opcoes['iconesAcoes'],$opcoes['idsAcoes'],$opcoes['dados'],
                $this->numItensPag, $this->classAcao, $this->manterLinhas);
            } else {
                $paginavel = new ListaPaginavel($items);
            }
        } else if ($tipo == 'tabela') {
            if ($opcoes) {
                $opcoes = array_replace(TabelaPaginavel::opcoesPadrao, $opcoes);
                $paginavel = new TabelaPaginavel($items, $opcoes['classe'],
                $opcoes['cabecalho'], $opcoes['executaAcao'],
                $opcoes['iconesAcoes'],$opcoes['idsAcoes'],$opcoes['dados'],
                $this->numItensPag, $this->classAcao, $this->manterLinhas);
            } else {
                $paginavel = new TabelaPaginavel($items);
            }
        }

        $this->paginavel = $paginavel;
    }

    public function desenhaPaginacao()
    {
        if ($this->totalItems > $this->numItensPag) {

            $paginacao = '<div id="paginacao_paginas'.$this->idAppend.'">';
        
            $chunkItems = array_chunk($this->items, $this->numItensPag);
            
            for ($pag=0; $pag<$this->numPaginas; $pag++) {

                $classPagina = $pag ? 'class="hide"' : null;

                $paginacao .= '<div id="paginacao_pagina'.$this->idAppend.'_'.$pag.'" '.$classPagina.'>';

                $this->paginavel->setItems($chunkItems[$pag]);

                $paginacao .= $this->paginavel->geraHTML();
                
                $paginacao .= '</div>';

            }

            $paginacao .= $this->desenhaIconesPaginacao();

            $paginacao .= '</div>';

            return $paginacao;

        } else {
            return $this->paginavel->geraHTML();
        }
    }

    private function desenhaIconesPaginacao()
    {
        $paginacao = '<ul class="pagination center-align"><li class="disabled">';
        $paginacao .= '<a href="#!" id="paginacao'.$this->idAppend.'_anterior">';
        $paginacao .= '<i class="material-icons">chevron_left</i></a></li>';
    
        for ($pag=0; $pag<$this->numPaginas; $pag++) {
            
            $class_paginacao = $pag ? 'class="waves-effect"' : 'class="active"';
            
            $paginacao .= '<li '.$class_paginacao.'>';
            $paginacao .= '<a href="#!" id="paginacao'.$this->idAppend.'_'.$pag.'">'.($pag+1).'</a></li>';
        }
        
        $paginacao .= '<li class="waves-effect">';
        $paginacao .= '<a href="#!" id="paginacao'.$this->idAppend.'_proximo">';
        $paginacao .= '<i class="material-icons">chevron_right</i></a></li></ul>';

        return $paginacao;
    }
    
}
