package com.globalnet.xml.parser;

import java.io.IOException;
import java.io.InputStream;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;

//import org.apache.crimson.tree.ElementNode;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.NodeList;
import org.xml.sax.SAXException;

public class XmlParser {

	private Document dom;

	public XmlParser(InputStream is){
		try {
			DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
			DocumentBuilder obj = dbf.newDocumentBuilder();
			dom = obj.parse(is);

		}catch(ParserConfigurationException e) {
			e.printStackTrace();
		}catch(SAXException e) {
			e.printStackTrace();
		}catch(IOException e) {
			e.printStackTrace();
		}
	}

/*
	private void parseDocument(){
		
		Element el;
		
		el = getNodeByPath("soap-env:Body/DeliverReq/Sender/Number");
		System.out.println(getTextFromElement(el));		
		
		el = getNodeByPath("soap-env:Body/DeliverReq/Recipients/To/Number");
		System.out.println(getTextFromElement(el));		
		
		el = getNodeByPath("soap-env:Header/TransactionID");
		System.out.println(getTextFromElement(el));		
		
		el = getNodeByPath("soap-env:Body/DeliverReq");
	
		System.out.println(getTextValue(el,"MMSRelayServerID"));
		System.out.println(getTextValue(el,"VASPID"));
		System.out.println(getTextValue(el,"VASID"));
		System.out.println(getTextValue(el,"LinkedID"));
		System.out.println(getTextValue(el,"TimeStamp"));
		System.out.println(getTextValue(el,"ReplyChargingID"));
		System.out.println(getTextValue(el,"Priority"));
		System.out.println(getTextValue(el,"Subject"));	
		
		System.out.println(getAttribute("soap-env:Body/DeliverReq/Content","href"));

	}
*/

	public String getAttribute(String path, String attribute){
		String attr = null;
		Element el = getNodeByPath(path);
		if (el != null) {
			attr = el.getAttribute(attribute);
		}
		return attr;
	}
	
	public String getTextFromElement(Element ele){
		String texto = "";
		if (ele != null && ele.hasChildNodes()){
			texto = ele.getFirstChild().getNodeValue();
		}
		return texto;
	}

	public String getTextValue(Element ele, String tagName) {
		String textVal = null;
		NodeList nl = ele.getElementsByTagName(tagName);
		if(nl != null && nl.getLength() > 0) {
			Element el = (Element)nl.item(0);
			if (el.hasChildNodes())
				textVal = el.getFirstChild().getNodeValue();
		}

		return textVal;
	}
	
	public String getTextValue(String tagName) {
		return getTextValue(dom.getDocumentElement(), tagName);
	}

	public Element getNodeByPath(String path){
		if (path.substring(0, 1).equals("/")) {
			path = path.substring(1);
		}
		String[] st = path.split("/");				
		return getNodeByPath(dom.getDocumentElement(), st, 0);
	}
	
	public Element getNodeByPath(Element element, String path){
		if (path.substring(0, 1).equals("/")) {
			path = path.substring(1);
		}
		String[] st = path.split("/");				
		return getNodeByPath(element, st, 0);
	}

	private String removeNamespace(String nodo){
		String[] str = nodo.split(":");
		return str[str.length-1];
	}

	private Element getNodeByPath(Element actual, String[] path, int i){
		Element nodo = null;
		
		NodeList nl = actual.getChildNodes();
		int items = nl.getLength();

		for (int h = 0; h < items; h++){
			if (nl.item(h) instanceof Element){
				nodo = (Element) nl.item(h);
				String nodeName = removeNamespace(nodo.getNodeName());
				String tokenActual = path[i];
				if (nodeName.equalsIgnoreCase(tokenActual)){
					i++;
					if (i > (path.length - 1)){
						return nodo;
					}				
					nodo = getNodeByPath(nodo, path, i);
					if (nodo != null){
						return nodo;
					}
				}
			}
		}
		return null;		
	}
	
}
