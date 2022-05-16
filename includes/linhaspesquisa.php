<?php

require_once 'paginacao.php';
require_once 'ppgplugincomponent.php';

class LinhasPesquisa extends PPGPluginComponent
{
    private $linhasPesquisa;
    private $JSONResponse;
    
    public function getJSONResponse()
    {
        return $this->JSONResponse;
    }
    
    public function setJSONResponse($json)
    {
        $this->JSONResponse = $json;
    }

    public function parseJSON($bodyItem)
    {
		foreach ($bodyItem['items'] as $itemData) {
			if (!array_key_exists($itemData['NomeAreaConcentracao'], $this->linhasPesquisa)) {
				$this->linhasPesquisa[$itemData['NomeAreaConcentracao']] = [$itemData['NomeLinhaConsulta'] => 
																			['Pesquisadores' => [$itemData['NomePesquisador']], 'Descricao' => $itemData['Descricao']]];
			} else {
				if (!array_key_exists($itemData['NomeLinhaConsulta'], $this->linhasPesquisa[$itemData['NomeAreaConcentracao']])) {
					$linhaPesquisaPPG = ['Pesquisadores' => [$itemData['NomePesquisador']], 'Descricao' => $itemData['Descricao']];
					$this->linhasPesquisa[$itemData['NomeAreaConcentracao']][$itemData['NomeLinhaConsulta']] = $linhaPesquisaPPG;
				} else {
					if ($itemData['Situacao'] == 'Em Andamento'){
						$this->linhasPesquisa[$itemData['NomeAreaConcentracao']][$itemData['NomeLinhaConsulta']]['Pesquisadores'][] = $itemData['NomePesquisador'];
					}
				}
			}
        }
    }
    
    public function renderLayout()
    {
        $HTMLfinal = '<div class="materialize-iso">
        <div class="row white-text">';

        foreach ($this->linhasPesquisa as $nomeConcentracao => $linhasConcentracao) {
            $HTMLfinal .= '<h4 class="white-text">'.$nomeConcentracao.'</h4>';
            $primeiro = true;
            $HTMLfinal .='<ul class="collapsible">';
            $id = 'a';
            foreach ($linhasConcentracao as $nomeLinha => $linhaPesquisa) {
                $icone = $primeiro ? 'do_not_disturb_on' : 'add_circle';
                $clasAtivo = $primeiro ? 'class="active"' : null;
                $HTMLfinal .= '<li '.$clasAtivo.'>';
                $HTMLfinal .= '<div class="collapsible-header black"><i class="material-icons">'.$icone.'</i>'.$nomeLinha.'</div>';
                $HTMLfinal .= '<div class="collapsible-body">';
                $HTMLfinal .= '<p class="white-text">'.$linhaPesquisa['Descricao'].'</p>';
                $HTMLfinal .= '<b class="white-text">Pesquisadores:</b>';
                $paginacao = new PaginacaoMaterialize($linhaPesquisa['Pesquisadores'], 'tabela', $id);
                $HTMLfinal .= $paginacao->desenhaPaginacao();
                $HTMLfinal .= '</li>';
                $primeiro = false;
                $id++;
            }
            $HTMLfinal .= '</ul>';
        }
        
        $HTMLfinal .= '</div></div>';

        print($HTMLfinal);
        
    }

}