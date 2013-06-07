<?php
/**
 * @package Update Firefox for Joomla!
 * @author Juan Darien Macías Hernández
 * @copyright (C) 2011 FirefoxMania.uci.cu
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access allowed to this file
defined( '_JEXEC' ) or die( 'Restricted access' );
 
// Import Joomla! Plugin library file
jimport('joomla.plugin.plugin');

// Comprobate Cookie
if(!isset($_COOKIE['actualiza_firefox'])){
	setcookie('actualiza_firefox', 'on', time()+60*60*24*10);
}

// Pull in the NuSOAP code
require_once('actualizafirefox/nusoap.php');

	//The System plugin Update Firefox
	class plgSystemActualizaFirefox extends JPlugin{
		/**
		* Constructor
		*/
		function plgSystemActualizaFirefox(& $subject, $params) {
			parent::__construct($subject, $params);
		}
		
		function onAfterRender()
		{
			// Create the client instance
			$client=new nusoap_client('http://firefoxmania.uci.cu/common/web_services/af.php?wsdl', true);

			// Call the SOAP method
			$result = $client->call('actualiza_firefox', array('input' => array('useragent'=>$_SERVER['HTTP_USER_AGENT'], 'ip'=>$_SERVER['REMOTE_ADDR'])));
			
			$url=$result['url'].'?af='.JURI::base();
			$url1=$result['url'].'?ot='.JURI::base();
			
			$app = JFactory::getApplication();
			$current_user =& JFactory::getUser();
			$body = JResponse::getBody();
	
			$descarga="<a class=\"vinculo\" target='blank_' href=$url >Actual&iacute;zalo</a>";
			$descarga0="<a class=\"vinculo\" target='blank_' href=$url1 >Descarga Firefox</a>";
			$cerrar="<a class=\"cerrar\" title='Cerrar' id=\"btnCerrar\" href='#' onclick='esconder()' >X</a>";
			$text='';
			$estilos='<div id="firefox"><div id="mensaje" class="mensaje"><center><div class="inner_padding">';
			$jquery="
			<script type=\"text/javascript\">
				function esconder(){
						var firefox = document.getElementById('firefox');
						var mensaje = document.getElementById('mensaje');
						firefox.removeChild(mensaje);
						writeCookie('actualiza_firefox', 'off', '240');  
				}
				function writeCookie(name, value, hours){
					var expire ='';
					if(hours != null){
						expire = new Date((new Date()).getTime() + hours * 3600000);
						expire = \"; expires=\" + expire.toGMTString();
					}
					document.cookie = name + \"=\" + escape(value) + expire;
				}
			</script>";

			if(stristr(JVERSION, '1.5')){
				$css = '<link rel="stylesheet" href="plugins/system/actualizafirefox/style.css" type="text/css" />';
			}else{
				$css = '<link rel="stylesheet" href="plugins/system/actualizafirefox/actualizafirefox/style.css" type="text/css" />';	
			}	
		
			if(isset($_COOKIE['actualiza_firefox'])){
				if ($_COOKIE['actualiza_firefox']=='on'){
					if ($result['browser']=='Firefox'){
						if ($result['flag']){
					
							$text=$estilos.'Hey! '.$current_user->name.' tu Firefox est&aacute; desactualizado. '.$descarga.$cerrar.'</div></center>';
						}
					}
					else{
					
						$text=$estilos.'Hey! '.$current_user->name.' Prueba el &uacute;nico navegador que te pone de primero. '.$descarga0.$cerrar.'</div></center>';
					}
				}
			}
		
			$codigo = $css.$text.$jquery;

			
			if(!($app->isAdmin() || strpos($_SERVER["PHP_SELF"], "index.php") === false)){
				$body = str_replace ("</body>", $codigo."</body>", $body);
				JResponse::setBody($body);
			}

			return true;
		}

	}



