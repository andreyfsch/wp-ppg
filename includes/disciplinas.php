<?php

require_once 'ppgplugincomponent.php';//use PPGPluginComponent;

class Disciplinas extends PPGPluginComponent
{
    private $disciplinas;
    private $titulosAtividades;
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
        $this->titulosAtividades = [];
		foreach ($bodyItem['disciplinas'] as $itemData) {
			if (!array_key_exists($itemData['Sigla'], $this->disciplinas)) {
				$this->disciplinas[$itemData['CodAtividadeEnsino']] = [
					'NomeAtividade' => $itemData['NomeDisciplina'],
					'SubTitulo' => $itemData['SubTitulo'],
					'Sumula' => $itemData['Sumula'],
					'UltimoSemestreOferecimento' => $itemData['UltimoSemestreOferecimento'],
					'Docentes' => [[
						'CodPessoa' => $itemData['CodPessoa'],
						'NomePessoa' => $itemData['NomePessoa'],
						'CodTurma' => $itemData['CodTurma'],
						'CargaHorSemTeorica' => $itemData['CargaHorSemTeorica'],
						'TipoAtividadeDocente' => $itemData['TipoAtividadeDocente']]
								  ]];
				if (is_null($itemData['SubTitulo'])) {
					$titulo = '<div style="width:90%"><h6 style="width:75%;display:inline-block;">'.$itemData['NomeAtividade'].'</h6><h6 style="float:right;display:inline-block;">'.$itemData['UltimoSemestreOferecimento'].'</h6></div>';
				} else {
					$titulo = '<div style="width:90%"><h6 style="display:inline-block;width:80%;">'.$itemData['NomeAtividade'].'</h6><h6 style="float:right;display:inline-block;">'.$itemData['UltimoSemestreOferecimento'];
					$titulo .= '</h6><span style="display:block; color:black">'.$itemData['SubTitulo'].'</span></div>';
				}
				$this->titulosAtividades[] = [$titulo, 'CodAtividadeEnsino' => $itemData['CodAtividadeEnsino']];
			} else {
				$this->disciplinas[$itemData['CodAtividadeEnsino']]['Docentes'][] = [
					'CodPessoa' => $itemData['CodPessoa'],
					'NomePessoa' => $itemData['NomePessoa'],
					'CodTurma' => $itemData['CodTurma'],
					'CargaHorSemTeorica' => $itemData['CargaHorSemTeorica'],
					'TipoAtividadeDocente' => $itemData['TipoAtividadeDocente']
				];
			}
		}
    }

    public function renderLayout()
    {
        $HTMLfinal = '<div class="materialize-iso">
        <div class="white-text">';

        $disciplinasPPG = new PaginacaoMaterialize($this->titulosAtividades,
            'lista',
            'discPPG', 
            ['dados' => ['CodAtividadeEnsino' => 'CodAtividadeEnsino',
                'href' => 'CodAtividadeEnsino'],
            'idsAcoes' => ['CodAtividadeEnsino' => 'modal'],
            'executaAcao' => true,
            'cabecalho' => '<div class="valign-wrapper"><b style="display:inline-block;width:70%;" class="flow-text">Disciplinas do Programa</b><b style="display:inline-block;float:right;width:30%" class="flow-text">Último Semestre de Oferecimento</b></div>'],
            20, 'disciplina-PPG', false);

        $HTMLfinal .= $disciplinasPPG->desenhaPaginacao();

        $modals = '';
        foreach ($this->disciplinas as $codAtividadeEnsino => $dadosAtividadeEnsino) {
            $docentes = [];
            foreach ($dadosAtividadeEnsino['Docentes'] as $dadosDocente) {
				$docentes[] = ['Nome' => $dadosDocente['NomePessoa'],
					'Função' => $dadosDocente['TipoAtividadeDocente'],
					'Turma' => $dadosDocente['CodTurma'],
					'Carga Horária Semanal' => $dadosDocente['CargaHorSemTeorica']];
            }
									
            $tabelaDocentes = new PaginacaoMaterialize($docentes,
                'tabela',
                'docentes_'.$codAtividadeEnsino,
                ['cabecalho' => ['Nome' => 75,
                        'Função' => 10,
                        'Turma' => 5,
                        'Carga Horária Semanal' => 10]
                ], 3);

            $modals .= '<div id="modal_'.$codAtividadeEnsino.'" class="modal" style="display:grid">';
			
			$modals .= '<i id="close_'.$codAtividadeEnsino.'" style="position:sticky;top:0.1em;right:0.1em;color:white;cursor:pointer;float:right" class="small material-icons">cancel</i>';
			
			$modals .= '<div style="grid-column:1;grid-row:1">';
			
			$modals .= '<b style="color:white;display:block;width:90%;" class="flow-text">'.$dadosAtividadeEnsino['NomeAtividade'].'</b>';
			
            $modals .= '<p class="flow-text" style="margin:1em">'.$dadosAtividadeEnsino['Sumula'].'</p>';
			
			$modals .= '</div>';

			$modals .= '<div style="grid-column:1;grid-row:2;margin:1em">';
			
            $modals .= '<h5 style="color:white">Professores</h5>';

            $modals .= $tabelaDocentes->desenhaPaginacao();

            $modals .= '</div></div>';
        }

        $HTMLfinal .= $modals;

        $HTMLfinal .= '</div></div>';

        print($HTMLfinal);
    }
}