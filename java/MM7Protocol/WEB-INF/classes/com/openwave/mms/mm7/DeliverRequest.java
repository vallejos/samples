package com.openwave.mms.mm7;

import java.util.Calendar;
import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.UnsupportedEncodingException;
import java.util.Vector;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import javax.mail.MessagingException;
import javax.mail.internet.MimeBodyPart;
import javax.mail.internet.MimeMultipart;
import javax.mail.internet.MimeUtility;
import javax.xml.transform.stream.StreamSource;

import org.w3c.dom.Element;

import com.globalnet.xml.parser.XmlParser;
import com.openwave.mms.mm7.util.Utils;

import com.openwave.mms.content.Factory;
import com.openwave.mms.content.ContentException;

/**
 *  This class encapsulates an MM7 DeliverRequest message that the application
 *  receives from MMSC. 
 *  <p> 
 *  When the application receives a DeliverRequest message, the API creates a 
 *  <code>DeliverRequest</code> object from the SOAP message and passes it
 *  to the <code>processDeliverRequest</code> method of a custom class that
 *  either implements the {@link MessageListener} interface or extends the 
 *  {@link MessageListenerAdapter} class. 
 *  <p>
 *  When processing DeliverRequest messages, follow these guidelines:
 *  <OL>
 *  <LI> Use the <code>DeliverRequest</code> accessors to retrieve data about the request.
 *  <LI> Save the <code>LinkedID</code>, if supplied, so that you can later include it in the 
 *       message you return to MMSC with the requested data.
 *  <LI> Use the <code>getContent</code> method to retrieve the actual multimedia message, as a
 *       <code>javax.mail.internet.MimeBodyPart</code> object, which contains the request.
 *  <LI> Use the <code>javax.mail.internet.MimeBodyPart</code> class methods to further 
 *       process the message and determine whether to accept or reject the request. 
 *       If you accept the message, create and return a {@link DeliverResponse} object; 
 *       otherwise create and return a {@link FaultResponse} object or throw a {@link
 *       MessageProcessingException} to have the API create the fault response for you.
 *  </OL>
 *  </p>
 *  For further information about using this class to process deliver requests from
 *  MMSC, including guidelines on accepting and rejecting messages, see the 
 *  <em>Openwave MMS Library Developer's Guide</em>.
 */ 
public class DeliverRequest {

    private String transactionID;
    private String linkedID;
    private String date;
    private String replyChargingID;
    private int priority = PRIORITY_NONE;
    private String subject;
    private Object content;
    private MimeBodyPart rawContent;
    private String sender;
    private Vector recipients;
    private String recipient;
    private String namespace;

    /** Integer constant that identifies the priority of this deliver request as low.
     *  This value is one of the possible priority levels returned by the 
     *  {@link #getPriority getPriority} method.
     */
    public static final int PRIORITY_LOW = 0;

    /** Integer constant that identifies the priority of this deliver request as high. 
     *  This value is one of the possible priority levels returned by the 
     *  {@link #getPriority getPriority} method.
     */
    public static final int PRIORITY_HIGH = 1;

    /** Integer constant that identifies the priority of this deliver request as normal. 
     *  This value is one of the possible priority levels returned by the 
     *  {@link #getPriority getPriority} method.
     */
    public static final int PRIORITY_NORMAL = 2;

    /** Integer constant that identifies that this deliver request does not have a priority level. 
     *  This value is one of the possible priority levels returned by the 
     *  {@link #getPriority getPriority} method.
     */
    public static final int PRIORITY_NONE = 3;

    /**
     *  Package-private constructor for a DeliverRequest object. The API constructs this
     *  object when it receives a Deliver PDU from the Relay. It then calls the
     *  processDeliverRequest method in the MessageListener object, if one was
     *  previously registered with the API.
     *
     *  @param multipart The MIME multipart from which the Deliver Request can be constructed.
     *  @exception APIException Thrown when the incoming MIME multipart does not have exactly
     *                          two(2) subparts as required by the standards document OR
     *                          when the SOAP part of the message cannot be parsed OR
     *                          when the SOAP message cannot be read
     *
     */
    DeliverRequest(MimeMultipart multipart) throws APIException, ContentException {
        try {
        	if (multipart.getCount() != 2)
                throw new APIException(ErrorCode.CLIENT_ERROR, "deliver-req-format-error");

            MimeBodyPart soapPart = (MimeBodyPart) multipart.getBodyPart(0);
//            StreamSource sopita = (StreamSource) soapPart.getContent();
            
            Object sopaObj = soapPart.getContent();
            InputStream is = null;
            if (sopaObj instanceof StreamSource) {
            	StreamSource sopita = (StreamSource) sopaObj;
            	is = sopita.getInputStream();
            } else if (sopaObj instanceof String) {
            	String sopita = (String) sopaObj;
            	is = new ByteArrayInputStream(sopita.getBytes());
            }                        

            rawContent = (MimeBodyPart) multipart.getBodyPart(1);

//            XmlParser parser = new XmlParser(sopita.getInputStream());
            XmlParser parser = new XmlParser(is);
            
    		Element element = parser.getNodeByPath("Header/TransactionID");
    		this.transactionID = parser.getTextFromElement(element);

            element = parser.getNodeByPath("Body/DeliverReq");

            this.namespace = "";
            
//            SOAPMethod method = parser.getEnvelope().getBody().getMethod();
//            namespace = method.getNamespace();

    		this.linkedID = parser.getTextValue(element, "LinkedID");
    		this.date = parser.getTextValue(element, "TimeStamp");
    		this.replyChargingID = parser.getTextValue(element, "ReplyChargingID");

    		String priorityStr = parser.getTextValue(element, "Priority");
            if (priorityStr != null) {
                if (priorityStr.equalsIgnoreCase("low"))
                    priority = PRIORITY_LOW;
                else if (priorityStr.equalsIgnoreCase("high"))
                    priority = PRIORITY_HIGH;
                else if (priorityStr.equalsIgnoreCase("normal"))
                    priority = PRIORITY_NORMAL;
            }

    		this.subject = parser.getTextValue(element, "Subject");	
    		
    		// -- ORIGINAL
    		element = parser.getNodeByPath(element, "Sender/Number");
    		this.sender = parser.getTextFromElement(element);
    		//Si el numero vino en formato internacional....
    		if(this.sender.length() > 9) {
    			this.sender = "0" + this.sender.substring(4, this.sender.length());	
    		}
    		
    		
    		
    		// -- FIX PARA COMCEL_CO
    		/*
    		element = parser.getNodeByPath(element, "Sender/RFC2822Address");
    		this.sender = parser.getTextFromElement(element);
    		Pattern pattern = Pattern.compile("\\+([\\d]+)/.*");
    		Matcher m = pattern.matcher(this.sender);
    		if (m.find()){
    			this.sender = m.group(1);
    		} else {
    			System.out.println("ERROR: No se pudo obtener el numero de remiente en " + DeliverRequest.class);
    		}*/
    		    		
    		this.content = parser.getAttribute("Body/DeliverReq/Content", "href");

    		// -- ORIGINAL
    		
    	//element = parser.getNodeByPath("Body/DeliverReq/Recipients/To/Number");
    		//Fix para ANCEL
    		element = parser.getNodeByPath("Body/DeliverReq/Recipients/To/ShortCode");
    		this.recipient = parser.getTextFromElement(element);
    		
    		/*
    		element = parser.getNodeByPath("Body/DeliverReq/Recipients/To/RFC2822Address");
    		this.recipient = parser.getTextFromElement(element);
    		m = pattern.matcher(this.recipient);
    		if (m.find()){
    			this.recipient = m.group(1);
    		} else {
    			System.out.println("ERROR: No se pudo obtener el numero de destino en " + DeliverRequest.class);
    		}*/
    		
    		   		
//            buildRecipientList(method);
            
        }catch(IOException ie) {
//            throw new APIException("io-exception-reading-from-multipart", ie.getMessage());
        	System.out.println(ie.getMessage());
        }catch(MessagingException me) {
//            throw new APIException("messaging-exception-retrieving", me.getMessage());
        	System.out.println(me.getMessage());
        }
    }

    /**
     *  Gets the ID that uniquely identifies this deliver request. Use this ID as the
     *  <code>TransactionID</code> of the response to the message so that MMSC can reconcile
     *  request and response pairs. 
     *
     *  @return The transaction ID of this request.
     */
    public String getTransactionID( ) { return transactionID; }

    /**
     *  Gets the message ID of this deliver request. Use this value as the <code>LinkedID</code>
     *  in subsequent messages that you submit to MMSC and that contain the requested content. 
     *
     *  @return The linked message ID of this request.
     */
    public String getLinkedID( ) { return linkedID; }

    /**
     *  Gets the priority of this deliver request.
     *
     *  @return The priority of this request, as one of the predefined values {@link #PRIORITY_LOW},
     *  {@link #PRIORITY_NORMAL}, {@link #PRIORITY_HIGH}, {@link #PRIORITY_NONE}.
     */
    public int getPriority( ) { return priority; }

    /**
     *  Gets the message ID of the original message submitted by the content provider that
     *  enabled reply-charging, to which this deliver request is a reply.
     *
     *  @return The ID that specifies the message to which this request is a reply.
     */
    public String getReplyChargingID( ) {
        return replyChargingID;
    }

    /**
     *  Gets the date and time that MMSC submitted this request to the content provider.
     *
     *  @return The date and time that this deliver request was submitted.
     *  @throws APIException If the API cannot convert the date and time from ISO-8601 format.
     */
    public Calendar getDate() throws APIException { 
        if( this.date == null ) return null;

        Calendar date = Utils.convertDateFromISO8601( this.date );
        if( date == null ) {
            throw new APIException( "error-parsing-date", this.date );
        }
        return date;
    }

    /**
     *  Gets the subject of this deliver request. The subject can contain text encoded 
     *  as specified in Request for Comment 2047: <em>MIME (Multipurpose Internet Mail Extensions) 
     *  Part Three: Message Header Extensions for Non-ASCII</em>.
     *
     *  @return The Subject of this request.
     *  @throws APIException If the API encounters an error while decoding the subject.
     */
    public String getSubject( ) throws APIException {
       try {
           return ( subject == null ) ? null 
                                      : MimeUtility.decodeText( subject );
       } catch( UnsupportedEncodingException uee ) {
           throw new APIException( "unsupported-encoding", uee.getMessage() );
       }
    }

    /**
     *  Gets the multimedia content of this deliver request.
     *  This method returns the content as a <code>MultimediaContent</code> object or
     *  a <code>javax.mail.internet.MimeBodyPart</code>. 
     *
     *  @return The multimedia content in this request.
     */
    public Object getContent() throws ContentException, APIException {
        try {
        	
        	if (rawContent.getContentType().startsWith("multipart")){
                MimeMultipart multipartContentPart = (MimeMultipart) rawContent.getContent();
                content = Factory.getInstance().newMultimediaContent(multipartContentPart);
            }else{
                content = rawContent;
            }
            
        }catch(IOException ie){
            throw new APIException("io-exception-reading-from-multipart", ie.getMessage());

        }catch(MessagingException me){
            throw new APIException("messaging-exception-retrieving", me.getMessage());
        }

        return content;
    }

    /**
     *  Gets the raw multimedia content of this deliver request.
     *  This method returns the content as a <code>javax.mail.internet.MimeBodyPart</code>. 
     *
     *  @return The multimedia content in this request as a MimeBodyPart.
     */
    public MimeBodyPart getRawContent( ) { return rawContent; }

    /**
     *  Gets the email address of the party that sent this deliver request.
     *
     *  @return The email address for the sender of this request.
     */
    public String getSender() { return sender; }

    /**
     *  Gets the list of one or more recipients of this deliver request.
     *
     *  @return A <code>Vector</code> of one or more {@link Recipient} objects for this request.
     */
    public Vector getRecipients() { return recipients; }

    public String getRecipient() { return recipient; }
    /**
     *  This method builds the recipients vector from the SOAP method passed in.
     *
     *  @param method the SOAPMethod fromwhich to extract the recipient list.
     *  @throws SOAPException if required elements cannot be extracted.
     *  @throws APIException if recipient addresses cannot be parsed.
     */
/*
    private void buildRecipientList(SOAPMethod method) throws SOAPException, APIException {
    	SOAPParameter recipients = method.getParameter(SOAPConsts.MM7RecipientsParameterName);
    	if (recipients == null) return;

    	Vector rcptVector = recipients.getParameters();
        if (rcptVector == null) return;
        
        this.recipients = new Vector();
        
        for (int i = 0; i < rcptVector.size(); i++) {
            SOAPParameter toCcBcc = (SOAPParameter) rcptVector.get(i);
            String paramName = toCcBcc.getName();
            Recipient.Type recipientType = null;
            if (paramName.equals(SOAPConsts.MM7ToParameterName)) {
                recipientType = Recipient.Type.TO;
            } else if( paramName.equals( SOAPConsts.MM7CcParameterName ) ) {
                recipientType = Recipient.Type.CC;
            } else if( paramName.equals( SOAPConsts.MM7BccParameterName ) ) {
                recipientType = Recipient.Type.BCC;
            } else continue;

            Vector addresses = toCcBcc.getParameters();
            if( addresses == null ) continue;
            for( int j = 0; j < addresses.size(); j++ ) {
                SOAPParameter address = ( SOAPParameter ) addresses.get( j );
                String addressType = address.getName();
                AddressType recipientAddrType = null;
                if( addressType.equals( SOAPConsts.MM7NumberParameterName ) ) {
                    recipientAddrType = AddressType.NUMBER;
                } else if( addressType.equals( SOAPConsts.MM7EmailParameterName ) ) {
                    recipientAddrType = AddressType.EMAIL;
                } else if( addressType.equals( SOAPConsts.MM7ShortCodeParameterName ) ) {
                    recipientAddrType = AddressType.SHORTCODE;
                } else continue;
                this.recipients.add( new Recipient( recipientType,
                                                    recipientAddrType,
                                                    address.getValue() ) );
            }
        }
    }
*/

    /**
     * Package private method used by the RelayConnection class
     * to relay the namespace from the request to response.
     */
    String getNamespace() {
        return namespace;
    }

}
