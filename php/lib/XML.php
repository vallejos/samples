<?PHP
	class XML {
		var $xml;
		var $encoding;
		var $header;
		function XML($p_name="root_node",$p_attributes=array(),$p_encoding=true,$p_header=true){
			$this->encoding = $p_encoding;
			$this->header = $p_header;
			$this->xml = new XMLNode($p_name,$p_attributes);
		}
		function firstChild(){
			return $this->xml;
		}
		function output(){
			if($this->header){
				header("Content-type:text/xml");
			}	
			return "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>".$this->xml->toString($this->encoding);
		}
	}
	class XMLNode{
		var $name;
		var $attributes;
		var $content;
		function XMLNode($p_name="node",$p_attributes=array(),$p_content=NULL){
			$this->name = join("_",explode(" ",$p_name));
			$this->attributes = $p_attributes;
			if($p_content!=null){
				$this->content = $p_content;
			}else{
				$this->content = array();
			}
		}
		function setTextNode($textnode){
			$this->content = $textnode;
		}
		function addChild($p_content){
			if(is_array($p_content)){
				$this->content = $p_content;
			}else if(is_string($p_content)){
				$this->content = $p_content;
			}else{
				array_push($this->content,$p_content);
			}
			
		}
		function setAttributes($p_attributes){
			$this->attributes = $p_attributes;
		}
		function setName($p_name){
			$this->name = $p_name;
		}
		function hasTextNode(){
			return is_string($this->content);
		}
		function toString($p_encode){
			$output = "";
			$output.= "<".$this->name;
			if(count($this->attributes)>0 && is_array($this->attributes)){
				$keys = array_keys($this->attributes);
				for($a=0; $a < count($keys); $a++){
					$output.= " ".$keys[$a]."=\"";
					$output.=($p_encode && is_string($this->attributes[$keys[$a]])) ? urlencode($this->attributes[$keys[$a]]):$this->attributes[$keys[$a]];
					$output.="\"";
				}
			}
			$output .= ">";
			if($this->hasTextNode()){
				$output .= "<![CDATA[";
				$output .=($p_encode)?urlencode($this->content):$this->content;
				$output .="]]>";
			}else{
				for($n=0;$n<count($this->content);$n++){
					$output .= $this->content[$n]->toString($p_encode);
				}
			}
			$output .= "</".$this->name.">";
			return $output;
		}
	}
?>