<?php
/**
* @package Partilhator
* @subpackage Content
* @copyright Copyright (C) 2011 Joomla Empresa & José A. Cidre Bardelás. All rights reserved.
* @license GNU/GPL, see LICENSE.php
*/
// no direct access
defined('_JEXEC') or die('Nom se pode aceder sem jeito nem modo');
jimport('joomla.event.plugin');

class plgContentpartilha extends JPlugin {
	function onPrepareContent(&$article, &$params, $limitstart) {
		if(JPluginHelper::isEnabled('content','partilha')==false) return;
		$mainframe= &JFactory::getApplication();
		if($mainframe->isAdmin()){
			JPlugin::loadLanguage('plg_content_partilha');
		} else {
			JPlugin::loadLanguage('plg_content_partilha', 'administrator');
		}
		$plugin = &JPluginHelper::getPlugin('content', 'partilha');
		$pluginParams = new JParameter($plugin->params);
		if ($pluginParams->def('excluir_secoes')) {
			$idSecom = explode(',', str_replace(' ', '', $pluginParams->def('excluir_secoes')));
			if (in_array($article->sectionid, $idSecom)) return;
		}
		if ($pluginParams->def('excluir_categorias')) {
			$idSecom = explode(',', str_replace(' ', '', $pluginParams->def('excluir_categorias')));
			if (in_array($article->catid, $idSecom)) return;
		}
		if ($pluginParams->def('excluir_artigos')) {
			$idSecom = explode(',', str_replace(' ', '', $pluginParams->def('excluir_artigos')));
			if (in_array($article->id, $idSecom)) return;
		}
		// $servicos[número] = array(tipo, nome, parâmetro, ligaçom, imagem);
		$servicos[1] = array('shakeit', 'Chuza!', 'ver_chuza', 'http://chuza.gl/submit.php?url=', 'jchuza_');
		$servicos[2] = array('shakeit', 'DoMelhor', 'ver_domelhor', 'http://domelhor.net/submit.php?url=', 'jdomelhor_');
		$servicos[3] = array('shakeit', 'La Tafanera', 'ver_latafanera', 'http://latafanera.cat/submit.php?url=', 'jlatafanera_');
		$servicos[4] = array('shakeit', 'Zabaldu', 'ver_zabaldu', 'http://zabaldu.com/submit.php?url=', 'jzabaldu_');
		$servicos[5] = array('shakeit', 'Aupatu', 'ver_aupatu', 'http://aupatu.com/'.$pluginParams->def('idioma_aupatu', 'eu').'/submit.php?url=', 'jaupatu_');
		$servicos[6] = array('shakeit', 'Meneame', 'ver_meneame', 'http://meneame.net/submit.php?url=', 'jmeneame_');
		$servicos[7] = array('shakeit', 'Tuenti', 'ver_tuenti', 'http://tuenti.com/share?url=', 'jtuenti_');
		$servicos[8] = array('buzzit', 'Buzz', 'ver_buzz', '', 'jbuzz_');
		$servicos[9] = array('shakeit', 'Digg', 'ver_digg', 'http://digg.com/submit?url=', 'jdigg_');
		$servicos[10] = array('shakeit', 'Facebook', 'ver_facebook', 'http://www.facebook.com/share.php?u=', 'jfacebook_');
		$servicos[11] = array('tweettit', 'Twitter', 'ver_twitter', 'http://twitter.com/share?url=', 'jtwitter_');
		$servicos[12] = array('shakeit', 'Cabozo', 'ver_cabozo', 'http://cabozo.com/share.php?url=', 'jcabozo_');
		$servicos[13] = array('inshare', 'In Share', 'ver_inshare', '', '');
		$servicos[14] = array('googleplus', 'Google +1', 'ver_googleplus', '', '');
		$ladrairo = '';
		$espaco_ico = $pluginParams->def('espaco_icones', 5);
		$tamanho_ico = $pluginParams->def('tamanho_icones', 24);
		$tamanho_ico_qua = $tamanho_ico.'x'.$tamanho_ico;
		$alinhamento_ico = $pluginParams->def('alinhamento_icones', 'left');

		// CSS -->
		$margemTopo = $pluginParams->def('margem_topo', 3).'px';
		$margemPe = $pluginParams->def('margem_pe', 3).'px';
		$margemEsquerda = $pluginParams->def('margem_esquerda', 3).'px';
		$margemDireita = $pluginParams->def('margem_direita', 3).'px';
		$estilo='';
		if ($pluginParams->def('posicom') == 'topo' || $pluginParams->def('posicom') == 'pe') {
			$estilo .= <<<REMATE
#partilhator {float: none; clear: both; width: 100%; height: 30px; margin: $margemTopo $margemDireita $margemPe $margemEsquerda;}
#partilhator.left {text-align: left;}
#partilhator.right {text-align: right;}
#partilhator.center {text-align: center;}
#partilhator img {border: none; margin: 0; padding: 0;}
#partilhator a, #partilhator a:hover, #partilhator a:visited, #partilhator a:link {text-decoration: none; margin: 0; padding: 0; background-color: transparent;}
#partilhator .partilhator_icone {margin-right: ${espaco_ico}px; background-color: transparent;}
REMATE;
		}
		if ($pluginParams->def('posicom') == 'esquerda' || $pluginParams->def('posicom') == 'direita') {
			$estilo .= <<<REMATE
#partilhator_lateral {width: 60px; margin:  $margemTopo $margemDireita $margemPe $margemEsquerda;}
#partilhator_lateral.floatleft {float: left;}
#partilhator_lateral.floatright {float: right;}
#partilhator_lateral img {border: none; margin: 0; padding: 0;}
#partilhator_lateral a, #partilhator_lateral a:hover, #partilhator_lateral a:visited, #partilhator_lateral a:link {text-decoration: none; margin: 0; padding: 0; background-color: transparent;}
#partilhator_lateral .separacom {display: block; height: ${espaco_ico}px; background-color: transparent;}
REMATE;
		}
		if ($pluginParams->def('posicom') == 'topo' || $pluginParams->def('posicom') == 'pe') {
			if ($pluginParams->def('ver_googleplus', 0))
				$estilo .= "\n#partilhator .partilhator_googleplus {display: inline-block;".($pluginParams->def('amplo_googleplus') ? " width: ".$pluginParams->def('amplo_googleplus')."px;" : "").($tamanho_ico == "24" ? "}" : " position: relative; bottom: 1px;}");
			if ($pluginParams->def('ver_inshare', 0))
				$estilo .= "\n#partilhator .partilhator_inshare {display: inline-block; margin-right: ".$espaco_ico."px; ".($tamanho_ico == "24" ? "position: relative; top: 2px;}" : "position: relative; top: 5px;}");
		}
		$doc = &JFactory::getDocument();
		$doc->addStyleDeclaration($estilo);
		// <-- CSS
		
		if ($pluginParams->def('posicom') == 'esquerda' || $pluginParams->def('posicom') == 'direita') {
			$canto = $pluginParams->def('posicom') == 'esquerda' ? 'left' : 'right';
			$ladrairo .= '<div id="partilhator_lateral" class="float'.$canto.'">';
			if ($pluginParams->def('ver_chuza', 1)) 
				$ladrairo .= '<a target="_blank" href="http://chuza.gl/submit.php?url='.rawurlencode(utf8_encode($this->plgGetPageUrl($article))).'"><img src="'.JURI::base().'plugins/content/partilha_imagens/jchuza.png" height="52" width="52"  alt="Chuza!" /></a><div class="separacom"></div>';
			if ($pluginParams->def('ver_domelhor', 0)) 
				$ladrairo .= '<a target="_blank" href="http://domelhor.net/submit.php?url='.rawurlencode(utf8_encode($this->plgGetPageUrl($article))).'"><img src="'.JURI::base().'plugins/content/partilha_imagens/jdomelhor.png" height="51" width="52"  alt="DoMelhor" /></a><div class="separacom"></div>';
			if ($pluginParams->def('ver_buzz', 0)) {
				if ($pluginParams->def('modo_buzz', 0)) {
					$articleURL = htmlspecialchars($this->plgGetPageUrl($article));
					$articleTitle = htmlspecialchars($article->title);
					$pageTitle = htmlspecialchars(JFactory::getConfig()->getValue('config.sitename'));
					$base = JURI::base(false);
					$pageURL = htmlspecialchars($base);
					$buzz = $articleURL.'&title='.$articleTitle.'&srcUrl='.$pageURL.'&srcTitle='.$pageTitle;
					if ($article->introtext) {
						$snippetLimit = (int) $pluginParams->def('longo_buzz', 1500);
						$buzz .= '&snippet='.$this->getArticleSnippet($article->introtext, $snippetLimit);
					}
					$ladrairo .= '<a style="margin-right: '.$espaco_ico.'px" target="_blank" href="http://www.google.com/reader/link?url='.$buzz.'"><img src="'.JURI::base().'plugins/content/partilha_imagens/jbuzz.png" height="62" width="52"  alt="BuzzIt" title="Buzz" /></a><div class="separacom"></div>';
				}
				else {
					$url = str_replace('//', '/', htmlspecialchars($this->plgGetPageUrl($article)));
					$url = str_replace('http:/', 'http://', $url);
					$title = urlencode($article->title);
					$ladrairo .= '<a style="margin-right: '.$espaco_ico.'px" target="_blank" href="http://www.google.com/buzz/post?message='.$title.'&url='.$url.'&imageurl="><img src="'.JURI::base().'plugins/content/partilha_imagens/jbuzz.png" height="62" width="52"  alt="BuzzIt" title="Buzz" /></a><div class="separacom"></div>';
				}
			}
			if ($pluginParams->def('ver_digg', 0)) 
				$ladrairo .= '<a target="_blank" href="http://digg.com/submit?url='.rawurlencode(utf8_encode($this->plgGetPageUrl($article))).'"><img src="'.JURI::base().'plugins/content/partilha_imagens/jdigg.png" height="52" width="52"  alt="DiggThis" /></a>';
			$ladrairo .= '</div>';
		}
		else {
			$ladrairo .= '<div id="partilhator" class="'.$alinhamento_ico.'">';
			if ($pluginParams->def('ordenar_icones', 0)) 
				$ordem = explode(",", $pluginParams->def('ordem_icones', '1,2,3,4,5,6,7,8,9,10,11,12,13,14'));
			else 
				$ordem = explode(",", '1,2,3,4,5,6,7,8,9,10,11,12,13,14');
			foreach ($ordem as $servico) {
				$ladrairo .= $this->geraCodigoBotoes($servicos, $servico, $pluginParams, $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article);
			}
			if (count($ordem) < 14) {
				for ($i = 1; $i < 15; $i++) {
					if (!in_array($i, $ordem)) {
					$ladrairo .= $this->geraCodigoBotoes($servicos, $i, $pluginParams, $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article);
					}
				}
			}
			$ladrairo .= '</div>';
		}
		if (JRequest::getVar('view') == "article") {
			if (($pluginParams->def('posicom') == 'pe')) 
				$article->text = $article->text.'<!-- Partilhator Plug-in Begin -->'.$ladrairo.'<!-- Partilhator Plug-in End -->';
			else 
				$article->text = '<!-- Partilhator Plug-in Begin -->'.$ladrairo.'<!-- Partilhator Plug-in End -->'.$article->text;
		}
	}

	function plgGetPageUrl(&$obj) {
		if (!is_null($obj)) {
			require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
			$url = JRoute::_(ContentHelperRoute::getArticleRoute($obj->slug, $obj->catslug, $obj->sectionid));
			$uri = &JURI::getInstance();
			$base = $uri->toString(array('scheme', 'host', 'port'));
			$url = $base.$url;
			$url = JRoute::_($url, true, 0);
			return $url;
		}
	}

	function getArticleSnippet(&$introText, $snippetLimit) {
		// Ensure that URLs are absolute
		$this->removeLocalURLs($introText, 'src="');
		$this->removeLocalURLs($introText, 'href="');
		$articleSnippet = htmlspecialchars($introText);
		// Trim snippet to avoid Google Reader API error
		if (strlen($articleSnippet) > $snippetLimit) {
			$articleSnippet = substr($articleSnippet, 0, $snippetLimit);
			$introText = htmlspecialchars_decode($articleSnippet);
			$this->cutOpenTag($introText, '<', '>');
			$this->cutOpenTag($introText, '<a', '</a>');
			$articleSnippet = htmlspecialchars($introText);
		}
		return $articleSnippet;
	}

	function removeLocalURLs(&$introText, $source) {
		$sourceLength = strlen($source);
		$sourcePos = strpos($introText, $source, 0);
		while ($sourcePos) {
			if (substr_compare($introText, 'http', $sourcePos + $sourceLength, 4) != 0) {
				$introText = substr_replace($introText, $source.JUri::base(false), $sourcePos, $sourceLength);
			}
			$sourcePos = strpos($introText, $source, $sourcePos + $sourceLength);
		}
	}

	function cutOpenTag(&$introText, $begin, $end) {
		$beginLastPos = strrpos($introText, $begin);
		$endLastPos = strrpos($introText, $end);
		if ($beginLastPos > $endLastPos) {
			$introText = substr($introText, 0, $beginLastPos);
		}
	}

	function geraCodigoBotoes($servicos, $indice, $pluginParams, $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article) {
		$treito = '';
		switch ($servicos[$indice][0]) {
			case 'shakeit':
				$treito .= plgContentpartilha::shakeit($servicos[$indice][1], $servicos[$indice][2], $servicos[$indice][3], $servicos[$indice][4], $pluginParams, $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article);
				break;

			case 'tweettit':
				$treito .= plgContentpartilha::tweettit($servicos[$indice][1], $servicos[$indice][2], $servicos[$indice][3], $servicos[$indice][4], $pluginParams, $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article);
				break;

			case 'buzzit':
				$treito .= plgContentpartilha::buzzit($servicos[$indice][1], $servicos[$indice][2], $servicos[$indice][3], $servicos[$indice][4], $pluginParams, $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article);
				break;

			case 'googleplus':
				$treito .= plgContentpartilha::googleplus($servicos[$indice][1], $servicos[$indice][2], $servicos[$indice][3], $servicos[$indice][4], $pluginParams, $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article);
				break;

			case 'inshare':
				$treito .= plgContentpartilha::inshare($servicos[$indice][1], $servicos[$indice][2], $servicos[$indice][3], $servicos[$indice][4], $this->params, $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article);
				break;
		}
		return $treito;
	}

	function shakeit($nome, $parametro, $ligacom, $imagem, $pluginParams, $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article) {
		if ($pluginParams->def($parametro, 1)) 
			return('<span class="partilhator_icone"><a href="'.$ligacom.rawurlencode(utf8_encode($this->plgGetPageUrl($article))).'" target="_blank"><img style="border: 0;" src="'.JURI::base().'plugins/content/partilha_imagens/'.$imagem.$tamanho_ico_qua.'.png" height="'.$tamanho_ico.'" width="'.$tamanho_ico.'"  alt="'.JText::_($nome).'" title="'.JText::_($nome).'" /></a></span>');
		else 
			return('');
	}

	function tweettit($nome, $parametro, $ligacom, $imagem, $pluginParams, $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article) {
		if ($pluginParams->def($parametro, 0) == 1) {
			// Novo sistema: o twitter reduce o tamanho das ligações e trata bem os diacríticos
			$url = rawurlencode(utf8_encode($this->plgGetPageUrl($article)));
			if (!$pluginParams->def('mensagem_twitter')) 
				$mensagemTwitter = $article->title;
			else 
				$mensagemTwitter = $pluginParams->def('mensagem_twitter');
			$title = urlencode($mensagemTwitter);
			$separator = $pluginParams->def('separator');
			$space = urlencode(' ');
			$tweet = $url;
			return('<span class="partilhator_icone"><a rel="nofollow" href="'.$ligacom.$tweet.'&text='.$pluginParams->def('sitio_curto_twitter', 'Web').': '.$title.'&via='.$pluginParams->def('sitio_longo_twitter', 'Web').'" target="_blank"><img style="border: 0;" src="'.JURI::base().'plugins/content/partilha_imagens/'.$imagem.$tamanho_ico_qua.'.png" height="'.$tamanho_ico.'" width="'.$tamanho_ico.'"  alt="'.JText::_($nome).'" title="'.JText::_($nome).'" /></a></span>');
		}
		else 
			return('');
	}

	function buzzit($nome, $parametro, $ligacom, $imagem, $pluginParams, $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article) {
		if ($pluginParams->def($parametro, 0)) {
			if ($pluginParams->def('modo_buzz', 0)) {
				$articleURL = htmlspecialchars($this->plgGetPageUrl($article));
				$articleTitle = htmlspecialchars($article->title);
				$pageTitle = htmlspecialchars(JFactory::getConfig()->getValue('config.sitename'));
				$base = JUri::base(false);
				$pageURL = htmlspecialchars($base);
				$buzz = $articleURL.'&title='.$articleTitle.'&srcUrl='.$pageURL.'&srcTitle='.$pageTitle;
				if ($article->introtext) {
					$snippetLimit = (int) $pluginParams->def('longo_buzz', 1500);
					$buzz .= '&snippet='.$this->getArticleSnippet($article->introtext, $snippetLimit);
				}
				return('<span class="partilhator_icone"><a href="http://www.google.com/reader/link?url='.$buzz.'" target="_blank"><img style="border: 0;" src="'.JURI::base().'plugins/content/partilha_imagens/'.$imagem.$tamanho_ico_qua.'.png" height="'.$tamanho_ico.'" width="'.$tamanho_ico.'"  alt="'.JText::_($nome).'" title="'.JText::_($nome).'" /></a></span>');
			}
			else {
				$url = str_replace('//', '/', htmlspecialchars($this->plgGetPageUrl($article)));
				$url = str_replace('http:/', 'http://', $url);
				$title = urlencode($article->title);
				return('<span class="partilhator_icone"><a href="http://www.google.com/buzz/post?message='.$title.'&url='.$url.'&imageurl=" target="_blank"><img style="border: 0;" src="'.JURI::base().'plugins/content/partilha_imagens/'.$imagem.$tamanho_ico_qua.'.png" height="'.$tamanho_ico.'" width="'.$tamanho_ico.'"  alt="'.JText::_($nome).'" title="'.JText::_($nome).'" /></a></span>');
			}
		}
		else 
			return('');
	}

	function googleplus($nome, $parametro, $ligacom, $imagem, $pluginParams, $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article) {
		if ($pluginParams->def($parametro, 0) == 1) {
			$tamanho = ($tamanho_ico == '24') ? 'standard' : 'small';
			$localFinal = '';
			if ($pluginParams->def('local_googleplus')) {
				$localFinal = $pluginParams->def('local_googleplus');
			}
			else {
				$local =& JFactory::getLanguage();
				$localCompleto = explode(',', $local->_metadata['locale']);
				$localSimples = explode('.', $localCompleto[0]);
				$localFinal = $localSimples[0];
			}
			return('<script src="http://apis.google.com/js/plusone.js" type="text/javascript">'.($localFinal ? '{lang:"'.str_replace('_', '-', $localFinal).'"}' : '').'</script><span class="partilhator_googleplus"><g:plusone size="'.$tamanho.'" href="'.utf8_encode($this->plgGetPageUrl($article)).'"></g:plusone></span>');
		}
		else 
			return('');
	}

	function inshare($nome, $parametro, $ligacom, $imagem, $pluginParams, $espaco_ico, $tamanho_ico, $tamanho_ico_qua, $article) {
		if ($pluginParams->def($parametro, 0) == 1) {
			$doc = &JFactory::getDocument();
			$doc->addScript("http://platform.linkedin.com/in.js");
			$tamanho = ($tamanho_ico == '24') ? 'standard' : 'small';
			return('<span class="partilhator_inshare"><script type="IN/Share" data-url="'.utf8_encode($this->plgGetPageUrl($article)).'" data-counter="right"></script></span>');
		}
		else 
			return('');
	}
}
