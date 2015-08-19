<?
class wmlMenu{

	var $goTo = "";
	var $menuText = "";
	var $options = array();

	function wmlMenu($goTo, $menuText, $options=array()){
		$this->goTo	    = $goTo;
		$this->menuText = $menuText;
		$this->options  = $options;
	}

	function toString(){
		$parrafo = new XMLNode("p");
		$parrafo->AddNode(new TextNode($this->menuText));
		$br = new TextNode("<br/>");
		foreach($this->options as $opt){
			$anchor = new Anchor($opt->text);
			$go = new Go($this->goTo);
			$pf_ser = new PostField("service",$opt->value);
			$go->AddComponent($pf_ser);
			$anchor->AddComponent($go);
			$parrafo->AddNode($anchor);
			$parrafo->AddNode($br);
		}
		return $parrafo->toString();
	}
}

class wmlMenuOption{

	var $text  = "";
	var $value = "";

	function wmlMenuOption($text,$value){
		$this->text  = $text;
		$this->value = $value;
	}
}

class wmlBack{

	var $goTo  = "";

	function wmlBack($goTo){
		$this->goTo  = $goTo;
	}

	function toString(){
		$parrafo = new XMLNode("p");
		$anchor = new Anchor("Volver");
		$go = new Go($this->goTo);
		$anchor->AddComponent($go);
		$parrafo->AddNode($anchor);
		return $parrafo->toString();
	}
}

class wmlMessage{

	var $service = "";
	var $backType = ""; //para wml

	function wmlMessage($service,$backType='deck'){
		$this->service = $service;
		$this->backType = $backType;
	}

	function toString(){
		$parrafo = new XMLNode("p");
		$parrafo->AddNode(new TextNode($this->service));
		$parrafo->AddNode(new TextNode("<br/>"));
		if($this->backType=="deck"){
			$anchor = new Anchor("Volver");
			$go = new Go(BACK_URL_DECK);
			$anchor->AddComponent($go);
			$parrafo->AddNode($anchor);
		}
		else if($this->backType=="page"){
			$parrafo->AddNode(new TextNode("<do type=\"accept\" label=\"Volver\"><prev/></do>"));
		}
		return $parrafo->toString();
	}

}

class wmlText{

	var $text = "";

	function wmlText($text){
		$this->text = $text;
	}

	function toString(){
		$parrafo = new XMLNode("p");
		$parrafo->AddNode(new TextNode($this->text));
		$parrafo->AddNode(new TextNode("<br/>"));
		return $parrafo->toString();
	}

}

class wmlInputs{
	var $goTo = "";
	var $submitText = "";
	var $inputs = array();
	var $vars = array();

	function wmlInputs($goTo, $submitText, $inputs, $vars=array()){
		$this->goTo = $goTo;
		$this->submitText = $submitText;
		$this->inputs = $inputs;
		$this->vars = $vars;
	}

	function toString(){
		$parrafo = new XMLNode("p");
		foreach($this->inputs as $inp){
			$parrafo->AddNode(new TextNode($inp->inputText."<br/>"));
			$input = new Input($inp->inputName);
			$parrafo->AddNode($input);
			$parrafo->AddNode(new TextNode("<br/>"));
		}
		$anchor = new Anchor($this->submitText);
		$go = new Go($this->goTo);
		foreach($this->inputs as $inp){
			$pf_ser = new PostField($inp->postName,"$(".$inp->inputName.")");
			$go->AddComponent($pf_ser);
		}
		foreach($this->vars as $var){
			$pf_ser = new PostField($var->postName,$var->value);
			$go->AddComponent($pf_ser);
		}
		$anchor->AddComponent($go);
		$parrafo->AddNode($anchor);
		return $parrafo->toString();
	}
}

class wmlVar{
	var $name = "";
	var $value = "";
	var $postName  = "";
	var $postValue  = "";

	function wmlVar($name, $value, $postName, $postValue){
		$this->name = $name;
		$this->value = $value;
		$this->postName  = $postName;
		$this->postValue  = $postValue;
	}
}

class wmlInput{
	var $inputName = "";
	var $inputText = "";
	var $postName  = "";

	function wmlInput($inputName, $inputText, $postName){
		$this->inputName = $inputName;
		$this->inputText = $inputText;
		$this->postName  = $postName;
	}
}

class wmlImage{
	var $xmlObj;
	var $alt;
	var $src;

	function wmlImage($src, $alt = ""){
		$this->src = $src;
		$this->alt = $alt;
	}



}

class wmlPage{

	var $xmlObj;
	var $card;
	var $title;
	var $elements = array();

	function wmlPage($elements, $title=''){
		$this->elements = $elements;
		$this->title = $title;
	}

	function addElement($element){
		array_push($this->elements,$element);
	}

	function toString(){
		$this->xmlObj = new XMLObject("wml");
		$this->xmlObj->AddNode(new TextNode("<head><meta http-equiv=\"Cache-Control\" content=\"max-age=0\" /></head>"));
		if($this->title==''){
			$this->card = new XMLNode("card");
		}
		else{
			$this->card = new XMLNode("card", array("title"=>$this->title));
		}
		foreach($this->elements as $element){
			$this->card->AddNode($element);
		}
		$this->xmlObj->AddNode($this->card);
		echo $this->xmlObj->toString();
	}
}
?>
