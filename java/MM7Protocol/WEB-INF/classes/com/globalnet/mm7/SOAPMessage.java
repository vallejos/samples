package com.globalnet.mm7;

import org.jdom.Element;
import org.jdom.Namespace;
import org.jdom.Document;
import org.jdom.input.DefaultJDOMFactory;
import org.jdom.output.XMLOutputter;

//import org.w3c.dom.Document;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import java.util.GregorianCalendar;
import java.io.IOException;
import java.io.StringWriter;

import javax.xml.soap.SOAPException;

public class SOAPMessage {
        
	public static Document getMessage(String ns, String strVASPID, String strVASID, String strShortCode, String strNumber, String strMessageSubject, boolean addTransactionID, boolean addHeaderNamespace, boolean addBodyNamespace) throws SOAPException {
		
    	String strXMLNS = "http://www.3gpp.org/ftp/Specs/archive/23_series/23.140/schema/REL-5-MM7-1-2";
            
        Element envelopeElementNode = new Element("Envelope");
        envelopeElementNode.setNamespace(Namespace.getNamespace(ns, "http://schemas.xmlsoap.org/soap/envelope/"));
        
        //#############################################################################
        //#######################     CREACION DEL HEADER     #########################
        //#############################################################################
        Element headerElementNode = new Element("Header");
        headerElementNode.setNamespace(addHeaderNamespace ? getNameSpase(ns, strXMLNS): Namespace.NO_NAMESPACE);
        
        Element transactionID = new Element("TransactionID");

		transactionID.setNamespace(Namespace.getNamespace(strXMLNS));
        transactionID.setAttribute("mustUnderstand", "1");
        if (addTransactionID){
        	transactionID.addContent(getTransactionId(strNumber, new Date()));
        }
        
        headerElementNode.addContent(transactionID);
        envelopeElementNode.addContent(headerElementNode);
    
        //#############################################################################
        //#######################      CREACION DEL BODY      #########################
        //#############################################################################
        Element bodyElementNode = new Element("Body");                                  
        bodyElementNode.setNamespace(addBodyNamespace ? getNameSpase(ns, strXMLNS): Namespace.NO_NAMESPACE);
        
        //#############################################################################
        //####################### CREACION DEL SUBMIT REQUEST #########################
        //#############################################################################
        Element submitReq = new Element("SubmitReq");

        submitReq.setNamespace(Namespace.getNamespace(strXMLNS));

        Element mm7Version = new Element("MM7Version");
        mm7Version.addContent("5.3.0");
        submitReq.addContent(mm7Version);
        
        
        //======================================
        //========= CREACION DEL SERNDER =======
        //======================================
        Element senderIdentification = new Element("SenderIdentification");
        
        Element vaspid = new Element("VASPID");
        vaspid.addContent(strVASPID);
        senderIdentification.addContent(vaspid);


        Element vasid = new Element("VASID");
        vasid.addContent(strVASID);
        senderIdentification.addContent(vasid);


        Element senderAddress = new Element("SenderAddress");


        Element number = new Element("Number");
        number.addContent(strShortCode);
        senderAddress.addContent(number);
        
        senderIdentification.addContent(senderAddress);
        submitReq.addContent(senderIdentification);
        //======================================


        //======================================
        //====== CREACION DEL RECIPIENTS =======
        //======================================
        Element recipients = new Element("Recipients");


        Element to = new Element("To");
        
        Element numberTo = new Element("Number");
        numberTo.addContent(strNumber);
        
        to.addContent(numberTo);
        
        recipients.addContent(to);
        submitReq.addContent(recipients);
        //======================================
        
        Element timeStamp = new Element("TimeStamp");
                              
        
        timeStamp.addContent(fechaIso());
        submitReq.addContent(timeStamp);


        Element expiryDate = new Element("ExpiryDate");
        GregorianCalendar cal = new GregorianCalendar();
        cal.setTime(new Date());
        cal.add(Calendar.DATE, 1);            
        expiryDate.addContent(fechaIso(cal.getTime()));
        submitReq.addContent(expiryDate);
        
        
        Element subject = new Element("Subject");
        subject.addContent(strMessageSubject);
        submitReq.addContent(subject);
        
        Element content = new Element("Content");
        content.setAttribute("href", "cid:" + getContentID());
        content.setAttribute("allowAdaptations", "true");
        submitReq.addContent(content);
        
        bodyElementNode.addContent(submitReq);
        envelopeElementNode.addContent(bodyElementNode);
        
        /*
        DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
		DocumentBuilder db = dbf.newDocumentBuilder();
		*/
        DefaultJDOMFactory dbf = new DefaultJDOMFactory();  
        
		Document dom = dbf.document(envelopeElementNode);

		return dom;
    }
	
    public static String getContentID() {
        return "generic_content_id";
    }
    
    private static String fechaIso(){
    	Date now = new Date();
    	return fechaIso(now);
    }
    
    private static String fechaIso(Date now){
        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss'Z'");
        return sdf.format( now ); 
    }
    
    private static String getTransactionId(String cellNumber, Date now){
        SimpleDateFormat sdf = new SimpleDateFormat("yyyyMMddHHmmss");
        return cellNumber + sdf.format(now); 
    }
    
    private static Namespace getNameSpase(String ns, String strXMLNS){
    	return Namespace.getNamespace(ns, strXMLNS);
    }

	public static String getStringFromDocument(Document doc, String ns) {
		String xml = null;
		try {
			XMLOutputter outputter = new XMLOutputter();
			StringWriter writer = new StringWriter();
			outputter.output(doc, writer);

			writer.close();
			
			xml = writer.toString();
			xml = xml.replaceAll(" xmlns=\"\"", "");
			xml = xml.replaceAll("Body xmlns:" + ns + "=\"", "Body xmlns=\"");
			xml = xml.replaceAll("Header xmlns:" + ns + "=\"", "Header xmlns=\"");
			
		} catch (IOException e) {
			e.printStackTrace();
		}
		return xml;
	}


	//##########################################################################

	public static void main(String[] args) {
    	String ns = "mms";
    	
		try {
			Document dom = getMessage(ns, "2002", "2002", "51195043517", "2002", "Subject", true, true, true);
			
			String soapText = getStringFromDocument(dom, ns);
			System.out.println(soapText);            

		}catch(SOAPException e) {
			System.out.println(e.getMessage());
		}
    }
    
	//##########################################################################
} 