<?php
	class complate {
		private	$br;
		public	$cheminBase;
		public	$data	=	array();
		public	$errors =	array();
		private	$fusion	=	false;
		public	$html;
		private	$htmldom;
		public	$template;
		public	$url;
		
		function __construct($template = false, $url = null) {
			$this->setBr();
			if($url)
				$this->setUrl($url);
			else
				$this->setUrlFull($_SERVER['REQUEST_URI']);
			if($template)
				$this->setTemplate($template);
		}
		
		private function adapterChemins() {
			if(!$base_href	=	$this->getCheminBase())
				$base_href	=	(strpos($this->template, '/') !== false) ? substr($this->template, 0, strrpos($this->template, '/')+1) : '';
			$tab_types	=	array(
				'img'		=>	'src',
				'script'	=>	'src',
				'link'		=>	'href'
			);
			foreach($tab_types as $balise => $attribute) {
				foreach($this->htmldom->find($balise) as $element) {
					$path		=	$element->$attribute;
					$tab_url	=	parse_url($path);
					if(!array_key_exists('scheme', $tab_url) && $path) {
						$element->$attribute	=	$base_href.$path;
					}
				}
			}
			//	On gère aussi les commentaires conditionnels IE
			$regCommIe		=	'#<!--\[if(.*)<!\[endif\]-->#Usi';
			$commentaires	=	$this->htmldom->find('comment');
			foreach($commentaires as $commentaire) {
				if(preg_match($regCommIe, $commentaire, $matches)) {
					$commentaire->innertext	=	preg_replace('#(href|src)=("|\')#', '$1=$2'.$base_href, $commentaire);
				}
			}
		}
		
		private function addBr() {
			$this->html	=	str_replace(array(PHP_EOL, "\n"), $this->getBr(), $this->html);
		}
		
		public function changeZone($zone, $html_zone) {
			$this->html	=	preg_replace('#<!-- '.strtoupper($zone).' -->(.*)<!-- '.strtoupper($zone).' -->#Usi', $html_zone, $this->html);
		}
		
		private function complate_attributes() {
			foreach($this->htmldom->find('[complate]') as $element) {
				$complate	=	$element->complate;
				//	On transforme le contenu en tableau
				$tab_complates	=	explode(',', $complate);
				foreach($tab_complates as $complate) {
					$complate	=	trim($complate);
					//	On détecte la présence d'un signe égal
					if(strpos($complate, '=') === false)
						$element->innertext	=	'#'.strtoupper($complate).'#';
					else {
						list($attribute, $value)	=	explode('=', $complate);
						$element->$attribute	=	'#'.strtoupper($value).'#';
					}
					$element->complate	=	null;
				}
			}
		}
		
		private function fillForms() {
			$tab	=	array('get', 'post');
			foreach($tab as $nom) {
				$request	=	($nom == 'get') ? $_GET : $_POST;
				// $this->print_t($request);
				$forms	=	$this->htmldom->find('form[method='.$nom.']');
				foreach($forms as $form) {
					//	On commence par décocher toutes les checkboxes
					if(!empty($request)) {
						foreach($form->find('input[type=checkbox], input[type=radio]') as $check)
							$check->checked	=	null;
					}
					foreach($request as $key => $value) {
						$champ	=	$form->find('[name='.$key.']', 0);
						if(!$champ) {
							$champ		=	$form->find('[name^='.$key.']', 0);	//	On tente de récupérer un array depuis un champ select multiple
							if($champ && str_replace('[]', '', $champ->name) != $key)
								continue;
						}
						if(!$champ)
							continue;
						$tag	=	$champ->tag;
						$type	=	$champ->type;
						switch($tag) {
							case 'input':
								switch($type) {
									case 'checkbox':
										$champ->checked	=	'checked';
										break;
									case 'radio':
										$tab_champ2	=	$this->htmldom->find('input[name='.$key.']');
										foreach($tab_champ2 as $champ2) {
											if($champ2->value == $value)
												$champ2->checked	=	'checked';
										}
										break;
									case 'password':
										$champ->value	=	'';
										break;
									default:
										$champ->value	=	stripslashes($value);
								}
								break;
							case 'select':
								if(!is_array($value))
									$value	=	array($value);
								foreach($value as $val) {
									$options	=	$champ->find('option');
									foreach($options as $option) {										
										if($option->value == $val)
											$option->selected	=	'selected';
									}
								}
								break;
							case 'textarea':
								$champ->innertext	=	stripslashes($value);
								break;
						}
					}
				}
			}
		}
		
		private function insererData() {
			if(!is_array($this->data))
				return false;
			//	On sépare les valeurs uniques des tableaux, pour mettre ces derniers avant
			$tmp_tab	=	$tmp_string	=	array();
			foreach($this->data as $key => $value) {
				if(is_array($value))
					$tmp_tab[$key]	=	$value;
				else
					$tmp_string[$key]	=	$value;
			}
			$this->data	=	array_merge($tmp_tab, $tmp_string);
			
			foreach($this->data as $zone => $content) {
				$pod	=	$this->getZone($zone, $this->html);
				if(is_array($content) && empty($content))
					$content	=	false;
				if(!is_array($content))
					$this->html	=	$this->setZone($zone, $content, $this->html);
				else {
					$contenu	=	'';
					$repeat		=	$this->getZone('repeat', $pod);
					$repeat_in	=	$this->getZone('repeat_in', $pod);
					foreach($content as $cle => $element) {
						if(!is_array($element))
							$pod	=	$this->setZone($cle, $element, $pod);
						else {
							if(array_key_exists('url', $element) && $this->getUrl() == $element['url'] && $repeat_in)
								$tmp	=	$repeat_in;
							else
								$tmp	=	$repeat;
							foreach($element as $key => $value) {
								$tmp	=	$this->setZone($key, $value, $tmp);
							}
							$contenu	.=	$tmp;
						}
					}
					$pod		=	$this->setZone('content', $contenu, $pod);
					$this->html	=	$this->setZone($zone, $pod, $this->html);
				}
			}
		}
		
		
		private function isData(&$tab) {
			//	En fonction des données fournies à l'objet, on ajoute de manière automatique (Si elles n'existent pas) les booléens is_valeur
			foreach($tab as $key => $value) {
				if(is_array($value) && !empty($value))
					$this->isData($tab[$key]);
				if(!preg_match('#is_.*#', $key)) {
					$is_data	=	true;
					if($value === null || $value === '' || empty($value) || $value === false)
						$is_data = false;
					$tab['is_'.$key]		=	$is_data;
					$tab['is_not_'.$key]	=	!$is_data;
				}
			}
		}
		
		private function print_t($tab) {
			echo '<pre>';
			print_r($tab);
			echo '</pre>';
		}
		
		public function useZone($zone) {
			$this->setHtml($this->getZone($zone, null, true), true);
		}
		
		//	Getters
		private function getBr() {
			return '<!-- BR_'.strtoupper($this->br).' -->';
		}
		
		public function getCheminBase() {
			return $this->cheminBase;
		}
		
		public function getData() {
			return $this->data;
		}
		
		public function getErrors() {
			if(empty($this->errors))
				return false;
			$html	=	<<<COMPLATE_ERRORS
				<div class="complate_errors">
					Complate has encountered some errors :
					<ul>
						#ERRORS#
					</ul>
				</div>
COMPLATE_ERRORS;
			foreach($this->errors as $key => $value)
				$this->errors[$key]	=	'<li>'.$value.'</li>';
			return str_replace('#ERRORS#', implode('', $this->errors), $html);
		}
		
		public function getHtml() {
			if($errors = $this->getErrors())
				return $errors;
			$this->addBr();
			$this->htmldom	=	new simple_html_dom();
			$this->htmldom->load($this->html);
			$this->adapterChemins();
			$this->fillForms();
			$this->complate_attributes();
			
			$this->html	=	$this->htmldom->save();
			$this->htmldom->clear();
			$this->insererdata();
			
			return str_replace($this->getBr(), PHP_EOL, $this->html);
			return $this->html;
		}
		
		public function getTemplate() {
			return $this->template;
		}
		
		public function getUrl() {
			return $this->url;
		}
		
		public function getZone($reg, $html = null, $keepComment = false) {
			if(!$html)
				$html	=	$this->html;
			if(preg_match('#<!-- '.strtoupper($reg).' -->(.*)<!-- '.strtoupper($reg).' -->#Usi', $html, $matches)) {
				$retour	=	$matches[!(int)$keepComment];
				$retour	=	str_replace($this->getBr(), PHP_EOL, $retour);
				return $retour;
			}
			return false;
		}
		
		//	Setters
		private function setBr($br = null) {
			if(!$br)
				$br	=	md5(rand());
			$this->br	=	$br;
		}
		
		public function setCheminBase($chemin = null) {
			if($chemin)
				$this->cheminBase	=	$chemin;
		}
		
		public function setData($data, $value = null) {
			if(!is_array($data) && $value !== null)
				$data	=	array($data => $value);
			if(is_array($data))
				$this->data	=	array_merge($this->data, $data);
			$this->isData($this->data);
		}
		
		private function setError($error) {
			$this->errors[]	=	$error;
		}
		
		public function setHtml($html, $interne = false) {
			$this->html	=	$html;
			if(!$interne) {
				$this->html	=	$this->getHtml();
			}
			$this->addBr();
		}
		
		public function setRSS($url) {
			return $this->setXML($url, 'channel');
		}

		public function setTemplate($template) {
			$this->template	=	$template;
			if(file_exists($this->template))
				$this->setHtml(file_get_contents($this->getTemplate()), true);
			else
				$this->setError('The template file path is not valid.');
		}
		
		public function setUrl($url) {
			$this->url	=	$url;
		}
		
		public function setUrlFull($url) {
			$tab	=	parse_url($url);
			$url	=	substr($tab['path'], strrpos($tab['path'], '/')+1, strlen($tab['path']));
			if(array_key_exists('query', $tab))
				$url	.=	'?'.$tab['query'];
			$this->url	=	$url;
		}		
		
		public function setXML($url, $base = '') {
			$xml	=	simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA);
			if($base) {
				foreach($xml->children() as $child) {
					if($child->getName() == $base) {
						$xml	=	$child;
						break;
					}
				}
			}
			$tab	=	array();
			foreach($xml as $key => $value) {
				if(!$value->children()) {
					$tab[$key]	=	(string)$value;
				}
				else {
					$tmp	=	array();
					foreach($value as $key1 => $value1) {
						$tmp[$key1]	=	(string)$value1;
						foreach($value1->attributes() as $keyattr => $valueattr)
							$tmp[$key1.'_'.$keyattr]	=	(string)$valueattr;
					}
					$tab[$key][]	=	$tmp;
				}
				foreach($value->attributes() as $keyattr => $valueattr)
					$tab[$key.'_'.$keyattr]	=	(string)$valueattr;
			}
			if(!empty($tab))
				$this->setData($tab);
		}
		
		public function setZone($reg, $contenu, $html = null) {
			if(!$html)
				$html	=	$this->html;
			if($contenu === true)
				return $html;
			$html	=	preg_replace('#<!-- '.strtoupper($reg).' -->(.*)<!-- '.strtoupper($reg).' -->#Usi', $contenu, $html);
			$html	=	str_replace('#'.strtoupper($reg).'#', $contenu, $html);
			return $html;
		}
	}
?>
