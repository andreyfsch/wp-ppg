<?php
/**
 * Classe construída para consumir a API v2 UFRGS.
 *
 * A classe se responsabiliza pela requisição HTTP à API, assim
 * como a eventual parametrização da mesma.
 *
 * * Markdown style lists function too
 * * Just try this out once
 *
 * The section after the description contains the tags; which provide
 * structured meta-data concerning the given element.
 *
 * @author  Andrey Felipe Schoier <andrey@cpd.ufrgs.br>
 *
 * @since 1.0
 */
class APIUFRGSHandler
{
   private const URL_BASE = 'https://api.ufrgs.br/v2/';
   private const URL_BASE_DEV = 'https://api.dev.ufrgs.br/v2/';
   private const URL_BASE_SVN = 'https://desenvolvimento.dsi/HomeSVN/';
   private const MODULO_API = 'pos-graduacao/';
   private const SVN_YII = 'api/web/';
   private const REQUEST_PARAMS = array('sslverify' => false);
   private const PPG_DEFAULT = 1313;
   
   private $modo;
   private $urlFinal;
   private $projetoSvn;
   private $homeSvn;
   private $endPoint;
   private $params;
   private $formato;
   
   public function consome_api(
      $endPoint, 
      $modo='prod', 
      $params=array(),
      $homeSvn=null, 
      $projetoSvn=null
   ){
      if (! is_null($homeSvn)) {
         $this->setHomeSvn($homeSvn);
      }
      
      if (! is_null($projetoSvn)) {
         $this->setProjetoSvn($projetoSvn);
      }
      
      try {
         $this->setEndPoint($endPoint);
         $this->setModo($modo);
         $this->setParams($params);
      } catch (Throwable $e) {
         echo 'Erro: '.$e->getMessage();
      }
      $this->setFormato();
            
      $this->montaURL();

		$response = wp_remote_get($this->urlFinal, self::REQUEST_PARAMS);
		
		$body = wp_remote_retrieve_body($response);
            
      return $body;
   }

   
   private function setEndPoint($endPoint)
   {
      $this->endPoint = $endPoint;
   }
   
   private function setModo($modo)
   {
      if (! in_array($modo, ['prod', 'dev', 'svn'])) {
         throw new Exception('Modo de consumo da API UFRGS Inválido.');
      }
      if (
         $modo == 'svn' 
         &&(! isset($this->projetoSvn) || ! isset($this->homeSvn))
      ) {
         throw new Exception('Modo de consumo da API UFRGS exige configuração do HomeSVN.');
      }
      
      $this->modo = $modo;
   }
   
   private function setHomeSvn($homeSvn)
   {
      $this->homeSvn = $homeSvn.'/';
   }
   
   private function setProjetoSvn($projetoSvn)
   {
      $this->projetoSvn = $projetoSvn.'/';
   }
   
   private function setFormato()
   {
      switch ($this->endPoint) {
         case 'disciplinas':
         case 'turmas':
         case 'teses':
         case 'docentes':
            $this->formato = 'pretty';
            break;
         default:
            $this->formato = 'get';
      }
   }
   
   private function setParams($params) {
      if (! is_array($params)) {
         throw new Exception('Parâmetros do endpoint devem ser passados como um array.');
      }
      
      if (empty($params)) {
         $this->params = ['programa' => self::PPG_DEFAULT];
      } else {
         $this->params = $params;
      }
   }
         
   private function montaURL()
   {
      if ($this->modo == 'prod') {
         $this->urlFinal = self::URL_BASE;		
      } elseif ($this->modo == 'dev') {
         $this->urlFinal = self::URL_BASE_DEV;
      } elseif ($this->modo == 'svn') {
         $this->urlFinal = self::URL_BASE_SVN;
         $this->urlFinal .= $this->homeSvn;
         $this->urlFinal .= $this->projetoSvn;
         $this->urlFinal .= self::SVN_YII;
      }
      
      $this->urlFinal .= self::MODULO_API;
      if ($this->formato == 'get') {
         $this->urlFinal .= 'programa/';
      }
      $this->urlFinal .= $this->endPoint;
      $this->urlFinal .= $this->resolveParamsUrl();
   }
         
   private function resolveParamsUrl()
   {
      $paramsUrl = '';
      $primeiro = true;
      foreach ($this->params as $nome => $valor) {
         if ($this->formato == 'get') {
            if ($primeiro) {
               $simboloUrl = '?';
            } else {
               $simboloUrl = '&';
            }
            
            $nomeUrl = $nome == 'programa' ? 'IdPrograma' : $nome; 
            
            $paramsUrl .= $simboloUrl.$nomeUrl.'='.$valor;
            
         } elseif ($this->formato == 'pretty') {
            
            $nomeUrl = $nome == 'IdPrograma' ? 'programa' : $nome; 
            
            $paramsUrl .= '/'.$nomeUrl.'/'.$valor;
         }
         
         if ($primeiro) {
            $primeiro = false;
         }
      }
      
      return $paramsUrl;
   }
}
