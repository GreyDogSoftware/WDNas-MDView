<?php
/*
Clase para el manejo dinamico de contenido
Moises Rodriguez P - GreyDog Systems 2014
*/
namespace GreyDogSoftware{
	class RenderCore{
		// Declaraciones de variables
		private $Content = "content"; // Paginas
		private $InvalidPageRedir = "errorpage.html"; // Pagina de error
		private $ReplaceTag = '#'; // Tag para reemplazo en plantilla
		private $DefaultPage = 'main'; // Pagina que se rendeara en caso de no encontrar una (debe estar definida).
		private $SupressErrorMsgs = false;
		private $Errors = array();
		private $PathDefinitions = array();
		private $GlobalDefs = array();
		// *******************************
		public function __construct($aParams){
			// Fijando carpeta para el contenido dinamico
			if (is_array($aParams)){
				if (key_exists('content', $aParams))$this->Content = $aParams['content'];
				if (key_exists('errorpage', $aParams)) $this->InvalidPageRedir = $aParams['errorpage'];
				if (key_exists('replacetag', $aParams)) $this->ReplaceTag = $aParams['replacetag'];
				if (key_exists('defaultpage', $aParams)) $this->DefaultPage = $aParams['defaultpage'];
				if (key_exists('pagedefinitions', $aParams)) $this->PathDefinitions = $aParams['pagedefinitions'];
	
				if (key_exists('supresserrors', $aParams)){
					if ($aParams['supresserrors'] === true){
						$this->SupressErrorMsgs = true;
					}else{
						$this->SupressErrorMsgs = false;
					}
				}
	
				if (key_exists('globals', $aParams)){
					foreach($aParams['globals'] as $Key=>$Value){
						//echo $Key . ' ' . $Value;
						$this->GlobalDefs['_global_'.$Key]=$Value;
					}
				}
			}
		}
	
		private function RaiseError($sMessage){
			$this->Errors[] = $sMessage;
			if ($this->SupressErrorMsgs === false){
				echo $sMessage . "\n";
			}
		}
	
		public function ShowPage(string $sPage){
			if(count($this->PathDefinitions) == 0){
				$this->RaiseError("<br><br>Error critico. No se puede utilizar esta funcion antes de definir la configuracion de la misma.<br>Se necesita el array \"pagedefinitions\"<br><br>Formato:'pagedefinitions'=>array(<br>&nbsp;&nbsp;&nbsp;&nbsp;'main'=>array('page'=>'Page.php'),<br>	)<br>");
				return 3;
			}

			if (key_exists($sPage, $this->PathDefinitions)){
				// Looking for the file
				$FileToLoad = $this->Content . '/' . $this->PathDefinitions[$sPage]['page'];
				$ErrorHandler = $this->Content . "/" . $this->InvalidPageRedir;
				// $PathDefinitions = $this->Content . '/' . $PathDefinitions;
				if (@file_exists($FileToLoad)){
					@include_once($FileToLoad);
					return 0; // Pagina encontrada y mostrada
				}else{
					if (@file_exists($ErrorHandler)){
						@include_once ($ErrorHandler);
						return 1; // Pagina no encontrada
					}else{
						$this->RaiseError("<br><br>Error critico. No se pudo encontrar la pagina solicitada ($FileToLoad). <br>Adicionalmente, la pagina de error ($ErrorHandler) tampoco se pudo encontrar.");
						return 2; // Error critico. Ni pagina solicitada ni pagina de error existen
					}
				}
			}else{
				$ErrorHandler = $this->Content . "/" . $this->InvalidPageRedir;
				if (@file_exists($ErrorHandler)){
					@include_once ($ErrorHandler);
					return 1; // Pagina no encontrada
				}else{
					$this->RaiseError("<br>Error. La pagina \"$sPage\" no se encuentra registrada. Revisa el archivo de definiciones de codigo antes de continuar. <br>Adicionalmente, la pagina de error ($ErrorHandler) tampoco se pudo encontrar.");
					return 2; // Error critico. Ni pagina solicitada ni pagina de error existen
				}
			}
		}
	
		public function RenderFullPage(string &$sPage){
			if (isset($sPage)){
				$this->ShowPage($sPage);
			}else{
				if ($this->DefaultPage != ''){
					$this->ShowPage($this->DefaultPage);
				}else{
					$this->RaiseError('<br>Para usar esta funcion se necesita definir la pagina por defecto en la configuracion<br>');
				}
			}
		}
	
		public function ParseTemplate(string $TemplateFile = null, array $DataArray = null, bool $DataToUTFCoding = false){
			$TemplateFile = $this->Content . '/' . $TemplateFile;
			if($TemplateFile != null){
				if (file_exists($TemplateFile)){
					$Template = file_get_contents($TemplateFile);
					// Replacing globals
					foreach($this->GlobalDefs as $Key=>$Value){
						$DataValue = $this->ResolveContent($Value);
						if ($DataToUTFCoding) $DataValue = utf8_encode($DataValue);
						$Template = str_replace($this->ReplaceTag . $Key . $this->ReplaceTag, $DataValue, $Template);
					}
					// Replacing locals
					if ($DataArray != null && is_array($DataArray)){
						foreach($DataArray as $Key=>$Value){
							$DataValue = $this->ResolveContent($Value);
							if ($DataToUTFCoding) $DataValue = utf8_encode($DataValue);
							$Template = str_replace($this->ReplaceTag . $Key . $this->ReplaceTag, $DataValue, $Template);
						}
					}
					return $Template;
				}
			}
			return false;
		}

		private function ResolveContent(string $Content){
			$Result = $Content;
			if(is_array($Result)){
				// Trying to resolve the value as an array
				if(key_exists('page',$Result)){

				}
			}elseif (strlen($Result)>7){
				// Trying to resolve the value as a file source
				if (strtolower(substr($Content,0,7))== 'file://'){
					$SrcPath = realpath($this->Content.'/'.substr($Result,7));
					if(file_exists($SrcPath)) $Result = file_get_contents($SrcPath);
				}				
			}
			return $Result;
		}

	}
}
?>