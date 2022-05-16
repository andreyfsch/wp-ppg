var collapsible = document.querySelectorAll('div.collapsible-header > i');

if (collapsible) {
	collapsible = Array.from(collapsible);
	collapsible.forEach(function(item, index, arr){
		item.parentElement.addEventListener('click', function(){
			var icone_atual = item.innerHTML;
			collapsible.map(function(i){i.innerHTML = 'add_circle';});
			if (icone_atual.startsWith('add_circle')) {
				item.innerHTML = 'do_not_disturb_on';
			} else {
				item.innerHTML = 'add_circle';
			}
		});
	});
}

function get_indice_ativo(paginacao)
{
	var indice_ativo;
	paginacao.forEach(function(active_item, active_index, active_arr){
		if (active_item.parentElement.className == 'active') {
			indice_ativo = active_index;
		}
	});
	
	return indice_ativo;
}

var paginacoes = document.querySelectorAll('div[id^="paginacao_paginas"]');

if (paginacoes) {
	 document.addEventListener('DOMContentLoaded', function() {
		var elems = document.querySelectorAll('.collapsible');
		var instances = M.Collapsible.init(elems);
	 });
	
	
	paginacoes.forEach(function(item, index, arr){
		var paginas = Array.from(item.querySelectorAll('div[id^="paginacao_pagina_"]'));
		var paginacao = Array.from(item.querySelectorAll('a[id^="paginacao_"]'));

		paginacao.forEach(function(pag_item, pag_index, pag_arr){
			if (!pag_item.id.endsWith('proximo') && !pag_item.id.endsWith('anterior')){
				pag_item.addEventListener('click', function() {
					paginas.map(function(pag){pag.className='hide'});
					paginas[pag_index-1].className='';
					paginacao.map(function(pag){pag.parentElement.className='waves-effect'});
					pag_item.parentElement.className='active';
					
					indice_ativo = get_indice_ativo(paginacao);
					if (indice_ativo == 1) {
						pag_arr[0].parentElement.className='disabled';
					} else if (indice_ativo == (pag_arr.length-2)) {
						pag_arr[(pag_arr.length-1)].parentElement.className='disabled';
					}
				});
			} else {
				pag_item.addEventListener('click', function() {
					if (pag_item.parentElement.className != 'disabled') {
						indice_ativo = get_indice_ativo(paginacao);
						if (pag_item.id.endsWith('proximo')) {
							paginas.map(function(pag){pag.className='hide'});
							paginas[indice_ativo].className='';
							paginacao.map(function(pag){pag.parentElement.className='waves-effect'});
							paginacao[indice_ativo+1].parentElement.className='active';
						} else {
							paginas.map(function(pag){pag.className='hide'});
							if (paginas[indice_ativo-2]) {
								paginas[indice_ativo-2].className='';
							}
							paginacao.map(function(pag){pag.parentElement.className='waves-effect'});
							paginacao[indice_ativo-1].parentElement.className='active';
						}

						indice_ativo = get_indice_ativo(paginacao);
						if (indice_ativo == 1) {
							pag_arr[0].parentElement.className='disabled';
						} else if (indice_ativo == (pag_arr.length-2)) {
							pag_arr[(pag_arr.length-1)].parentElement.className='disabled';
						}
					}
				});
			}
			
		});
	});
}

var modals = document.querySelectorAll('.modal');

if (modals) {
	 document.addEventListener('DOMContentLoaded', function() {
		var elems = document.querySelectorAll('.modal');
    	var instances = M.Modal.init(elems, {'startingTop':'5%', 'endingTop': '10%'});
	
		
		modals.forEach(function(item, index, arr){
			var close_btn = item.querySelector('i');
			close_btn.addEventListener('click', function() {
				var cod = this.id.split('_')[1];
				var elem = document.querySelector('#modal_'+cod);
				var instance = M.Modal.getInstance(elem);
				instance.close();
			}, false);
		});
	});
}