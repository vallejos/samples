package com.openwave.mms.mm7;

import java.io.ByteArrayOutputStream;
import java.util.Calendar;
import java.util.Enumeration;
import java.util.HashMap;
import java.io.IOException;
import java.io.OutputStream;
import java.util.Properties;
import java.io.UnsupportedEncodingException;
import java.util.Vector;
import java.util.List;

import javax.mail.Header;
import javax.mail.MessagingException;
import javax.mail.internet.MimeBodyPart;
import javax.mail.internet.MimeMessage;
import javax.mail.internet.MimeMultipart;
import javax.mail.internet.MimeUtility;
import javax.mail.Session;

import org.apache.log4j.Logger;
import org.apache.log4j.Level;

import com.openwave.mms.mm7.soap.SOAPBody;
import com.openwave.mms.mm7.soap.SOAPConsts;
import com.openwave.mms.mm7.soap.SOAPEnvelope;
import com.openwave.mms.mm7.soap.SOAPException;
import com.openwave.mms.mm7.soap.SOAPMethod;
import com.openwave.mms.mm7.soap.SOAPParameter;
import com.openwave.mms.mm7.soap.SOAPQName;
import com.openwave.mms.mm7.util.Utils;
import com.openwave.mms.mm7.util.Enum;

import com.openwave.mms.content.ContentException;
import com.openwave.mms.content.Factory;
import com.openwave.mms.content.MultimediaContent;

/**
 * This class encapsulates an MM7 SubmitRequest message that contains multimedia 
 * content and which the application sends to MMSC for delivery to mobile subscribers. 
 * <p>
 * To use this class:
 * <OL>
 * <LI> Create a <code>SubmitRequest</code> object.
 * <LI> Set the mandatory <code>Recipients</code> field using either the 
 *      <code>addRecipient</code> or <code>addRecipients</code> method, depending
 *      on whether you want to add one or more than one recipient at a time.
 * <LI> Set additional data elements that you require.
 * <LI> Create an array of <code>javax.mail.internet.MimeBodyPart</code> objects,
 *      using standard JavaMail classes, that define the multimedia message content
 *      and assign it to the <code>SubmitRequest</code> object.
 * <LI> Use the {@link RelayConnection} class' <code>sendRequest</code> method to send the
 *      submit request to the carrier.
 * </OL>
 * </p>
 * For further information about using this class to create and send submit requests
 * to the carrier, see the <em>Openwave MMS Library Developer's Guide</em>.
 */ 
public final class SubmitRequest extends Request {

    /**
     *  This inner class encapsulates the constants that identify the message class.
     */
    public static class MessageClass extends Enum {
        /**
         *  Private constructor ensures that only values created herein are used.
         *
         *  @param messageClass The message class value.
         */
        private MessageClass( String messageClass ) {
            super( messageClass );
        }
    
        /**
         *  Returns the message class for this submit request as a <code>String</code> object.
         *
         *  @return The message class as a <code>String</code> object.
         */
        public static MessageClass valueOf( String messageClass ) {
            return ( MessageClass ) allowedValues.get( messageClass.toLowerCase() );
        }
    
        /**
         *  String constant that identifies the class of this message as personal.
         */
        public static final MessageClass PERSONAL =
               new MessageClass( SOAPConsts.MM7PersonalParameterValue );
    
        /**
         *  String constant that identifies the class of this message as informational.
         */
        public static final MessageClass INFORMATIONAL =
               new MessageClass( SOAPConsts.MM7InformationalParameterValue );
    
        /**
         *  String constant that identifies the class of this message as advertisement.
         */
        public static final MessageClass ADVERTISEMENT =
               new MessageClass( SOAPConsts.MM7AdvertisementParameterValue );
    
        /**
         *  String constant that identifies the class of this message as automatic.
         */
        public static final MessageClass AUTO =
               new MessageClass( SOAPConsts.MM7AutoParameterValue );
    }

    /**
     *  This inner class encapsulates the constants that identify the message priority.
     */
    public static class Priority extends Enum {
        /**
         *  Private constructor ensures that only values created herein are used.
         *
         *  @param priority The priority value.
         */
        private Priority( String priority ) {
            super( priority );
        }
    
        /**
         *  Returns a <code>Priority</code> object based on the value of the priority 
         *  <code>String</code> value supplied.
         *
         *  @return <code>Priority</code> object that corresponds to a priority 
         *  <code>String</code> value.
         */
        public static Priority valueOf( String priority ) {
            return ( Priority ) allowedValues.get( priority.toLowerCase() );
        }
    
        /**
         *  String constant that identifies the priority of this message as low.
         */
        public static final Priority LOW = new Priority( SOAPConsts.MM7PriorityLowParameterValue );
    
        /**
         *  String constant that identifies the priority of this message as high.
         */
        public static final Priority HIGH = new Priority( SOAPConsts.MM7PriorityHighParameterValue );
    
        /**
         *  String constant that identifies the priority of this message as normal.
         */
        public static final Priority NORMAL = new Priority( SOAPConsts.MM7PriorityNormalParameterValue );
    }

    /**
     *  This inner class encapsulates the constants that indentify
     *  the party charged for the message.
     */
    public static class ChargedParty extends Enum {
        /**
         *  Private constructor ensures that only values created herein are used.
         *
         *  @param chargedParty The charged party value.
         */
        private ChargedParty(String chargedParty) {
            super(chargedParty);
        }
    
        /**
         *  Returns the party charged for this message as a <code>String</code> object.
         *
         *  @return The party charged for the request as a <code>String</code> object.
         */
        public static ChargedParty valueOf(String chargedParty) {
            return (ChargedParty) allowedValues.get(chargedParty.toLowerCase());
        }
    
        /**
         *  String constant that identifies the sender as the party charged for 
         *  this message.
         */
        public static final ChargedParty SENDER = new ChargedParty(SOAPConsts.MM7ChargedPartySenderParameterValue);
    
        /**
         *  String constant that identifies the recipient as the party charged for 
         *  this message. 
         */
        public static final ChargedParty RECIPIENT = new ChargedParty(SOAPConsts.MM7ChargedPartyRecipientParameterValue);
    
        /**
         *  String constant that identifies both the sender and the recipient as the 
         *  parties charged for this message. 
         */
        public static final ChargedParty BOTH = new ChargedParty(SOAPConsts.MM7ChargedPartyBothParameterValue);
    
        /**
         *  String constant that identifies neither the sender nor the recipient is 
         *  charged for this message. 
         */
        public static final ChargedParty NEITHER = new ChargedParty(SOAPConsts.MM7ChargedPartyNeitherParameterValue);
    }

    /**
     *  Instantiates a <code>SubmitRequest</code> object.
     */
    public SubmitRequest() {
        recipients = new Vector();
        replyChargingSize = -1;
        deliveryReport = null;
        readReply = null;
        distributionIndicator = null;
    }

    /**
     *  Adds a single recipient to this message. 
     *
     *  @param recipientType Type of recipient. Must be one of values defined by the 
     *         {@link Recipient.Type} class.
     *  @param recipientAddressType Type of recipient address. Must be one of values defined by the
     *         {@link AddressType} class.
     *  @param recipientAddress Address of recipient.
     *  @exception APIException  If the value of <code>recipientType</code> or <code>recipientAddrType</code> is 
     *             not one of the defined constants, or if an error occurs when parsing the value of
     *             <code>recipientAddr</code> when declared as type <code>AddressType.EMAIL</code>.
     */
    public void addRecipient( Recipient.Type recipientType,
                              AddressType recipientAddressType,
                              String recipientAddress ) throws APIException {
        recipients.add( new Recipient( recipientType,
                                       recipientAddressType,
                                       recipientAddress ) );
    }

    /**
     *  Adds several recipients at a time to this message.
     *
     *  @param recipients <code>Vector</code> of Recipient objects to add to this message.
     */
    public void addRecipients( Vector recipients ) {
        this.recipients.addAll( recipients );
    }

    /**
     *  Sets the service code for this message to specify content-provider-specific 
     *  information for billing purposes.
     *  <p>
     *  MMSC stores this value in the <code>vasServiceCode</code> field of charging data 
     *  records (CDRs). You can use this value to aid the process of reconciling copies of 
     *  provider-related CDRs.
     *
     *  @param serviceCode The service code for this request.
     */
    public void setServiceCode( String serviceCode ) {
        this.serviceCode = serviceCode;
    }

    /**
     *  Sets the linked message ID for this message. Specify the ID of a message 
     *  previously received and for which this message is submitted in response. 
     *
     *  @param linkedID The linked message ID for this request.
     */
    public void setLinkedID( String linkedID ) { this.linkedID = linkedID; }

    /**
     *  Sets the message class for this message. 
     *
     *  @param messageClass The message class for this request. Must be one of values defined 
     *         by the {@link SubmitRequest.MessageClass} class.
     */
    public void setMessageClass( MessageClass messageClass ) {
        this.messageClass = messageClass;
    }

    /**
     *  Sets the message priority for this message.
     *
     *  @param priority The priority of the message. Must be one of values defined 
     *         by the {@link SubmitRequest.Priority} class.
     */
    public void setPriority( Priority priority ) {
        this.priority = priority;
    }

    /**
     *  Sets the charged party for this message.
     *
     *  @param chargedParty The charged party for this message. Must be one of values defined 
     *         by the {@link SubmitRequest.ChargedParty} class.
     */
    public void setChargedParty( ChargedParty chargedParty ) {
        this.chargedParty = chargedParty;
    }

    /**
     *  Sets the maximum size, in bytes, of a recipient reply to this message.
     *  This value is used only when the message uses reply-charging to charge
     *  the content provider for replies.
     *
     *  @param replyChargingSize The size of the reply message.
     */
    public void setReplyChargingSize( int replyChargingSize ) {
        if( replyChargingSize > 0 )
            this.replyChargingSize = replyChargingSize;
    }

    /**
     *  Sets the deadline after which the carrier does not accept replies to this message. 
     *  This value is used only when the message uses reply-charging to charge the content 
     *  provider for replies.
     *
     *  @param replyDeadline The deadline by which the recipient must reply.
     */
    public void setReplyDeadline( Calendar replyDeadline ) {
        this.replyDeadline = Utils.convertDateToISO8601( replyDeadline );
    }

    /*
     *  Sets the earliest delivery time for a deferred delivery
     *  message. The relay will deliver the message only after this time
     *  has passed.
     *
     *  @param earliestDeliveryTime The time after which the message can be delivered.
     */
    public void setEarliestDeliveryTime( Calendar earliestDeliveryTime ) {
        this.earliestDeliveryTime = Utils.convertDateToISO8601( earliestDeliveryTime );
    }

    /**
     *  Sets the point in time after which the carrier does not attempt to deliver 
     *  this message.
     *
     *  @param expiry The time after which the message is not delivered.
     */
    public void setExpiry( Calendar expiry ) {
        this.expiry = Utils.convertDateToISO8601( expiry );
    }

    /**
     *  Sets whether a delivery report is requested for this message. If one is requested,
     *  the carrier generates one and delivers it to the sender after delivering
     *  the message to the recipient.
     *
     *  @param deliveryReport  A Boolean value that specifies whether a delivery report is requested.
     *                         <code>true</code> indicates that a report is requested.
     *                         The default is <code>false</code>, a report is not requested. 
     */
    public void setDeliveryReport( boolean deliveryReport ) {
        this.deliveryReport = new Boolean( deliveryReport );
    }

    /**
     *  Sets whether a read-reply report is requested for this message. If one is requested, 
     *  the recipient user agent generates one after downloading the message and
     *  the carrier delivers the report to the sender.
     *
     *  @param readReply A Boolean value that specifies whether a read-reply report is requested. 
     *                         <code>true</code> indicates that a report is requested.
     *                         The default is <code>false</code>, a report is not requested. 
     */
    public void setReadReply( boolean readReply ) {
        this.readReply = new Boolean( readReply );
    }

    /**
     *  Sets whether recipients can redistribute this message.
     *
     *  @param distributionIndicator A boolean value that specifies whether recipients can redistribute
     *         this message. <code>true</code> indicates that recipients can redistribute this 
     *         message. The default is <code>false</code>, recipients cannot redistribute this 
     *         message.
     */
    public void setDistributionIndicator( boolean distributionIndicator ) {
        this.distributionIndicator = new Boolean( distributionIndicator );
    }

    /**
     *  Sets the subject field of this message. 
     *  <p>
     *  The subject is encoded into the default charset of the platform on which the API  
     *  runs. It is encoded according to the protocol defined in Request For Comments (RFC)2047:
     *  <em>MIME (Multipurpose Internet Mail Extensions) Part Three: Message Header Extensions for 
     *  Non-ASCII Text</em>.</p>
     *
     *  @param subject The subject of this message.
     *  @exception APIException If an error occurs while encoding the value.
     */
    public void setSubject( String subject )
                            throws APIException {
        try {
            if( subject != null ) {
                this.subject = MimeUtility.encodeText( subject, "UTF-8", "B" );
            }
        } catch( UnsupportedEncodingException uee ) {
            throw new APIException( "unsupported-encoding", uee.getMessage() );
        }
    }

    /**
     *  Sets the subject field of this message. 
     *  <p>
     *  The subject is encoded into the supplied charset. It is encoded according to the protocol 
     *  defined in Request For Comments (RFC)2047:
     *  <em>MIME (Multipurpose Internet Mail Extensions) Part Three: Message Header Extensions for 
     *  Non-ASCII Text</em>.</p>
     *
     *  @param subject The subject of this message.
     *  @param charset The charset of this message. Any charset supported by Java is a valid value.
     *  @exception APIException If an error occurs while encoding the value.
     */
    public void setSubject( String subject, String charset )
                            throws APIException {
        try {
            if( subject != null ) {
                this.subject = MimeUtility.encodeText( subject, charset, "B" );
            }
        } catch( UnsupportedEncodingException uee ) {
            throw new APIException( "unsupported-encoding", uee.getMessage() );
        }
    }

    /**
     *  Sets the multimedia message content of this message. The parameter
     *  can be an instance of <code>List</code> where it is expected to
     *  contain a list of <code>Slide</code>s or <code>MultimediaContent</code>
     *  or <code>javax.mail.MimeBodyPart</code> or <code>javax.mail.MimeMultipart</code>
     *
     *  @param content The multimedia content of this message as a List of 
     *         <code>Slide</code> objects created using the composition api.
     *  @throws IllegalArgumentException if the object passed in is not one
     *          of the types listed above.
     *  @throws ContentException If there is an error creating the <code>MultimediaContent</code>
     *          object from the object passed in.
     *  @throws IOException If there is an error getting content from the
     *          object passed in.
     *  @throws MessagingException If the object passed in is a JavaMail object
     *          and there is an error processing it.
     *          
     */
    public void setContent(Object content) throws ContentException, MessagingException, IOException {

    	if (content instanceof List) {
            this.content = Factory.getInstance().newMultimediaContent();
            ((MultimediaContent) this.content).setSlides((List) content);
            
        }else if (content instanceof MultimediaContent) {
            this.content = (MultimediaContent) content;
            
        }else if (content instanceof MimeBodyPart) {
            this.content = content;
            
        }else if (content instanceof MimeMultipart) {
            this.content = Factory.getInstance().newMultimediaContent((MimeMultipart) content);
            
        }else if (content instanceof MimeBodyPart[]) {
            MimeBodyPart[] parts = (MimeBodyPart[]) content;
            MimeMultipart multipart = new MimeMultipart("related");
            
            for (int i = 0; i < parts.length; i++) {
                multipart.addBodyPart(parts[i]);
            }
            
            this.content = Factory.getInstance().newMultimediaContent(multipart);
            
        }else{
            throw new IllegalArgumentException("illegal-content-in-setcontent");
        }
    }

    /**
     *  Protected method that the {@link Request} class uses to write this message 
     *  to an <code>OutputStream</code> object. This method is for API internal use only.
     *
     *  @param outputStream The <code>OutputStream</code> object to which this object is written.
     *  @throws APIException If the API encounters an error while composing the message from MIME 
     *          body parts or while writing the message to the output stream.
     *  @throws SOAPException If any of the SOAP Envelope creation rules are violated.
     *  @throws ContentException If the content processing results in an error.
     *
     */
    protected void writeTo(OutputStream outputStream) throws APIException, ContentException, SOAPException {
        SOAPEnvelope env = new SOAPEnvelope(null);
        SOAPMethod method = new SOAPMethod(new SOAPQName(SOAPConsts.MM7SubmitReqMethodName, "mm7", mm7Namespace), (Vector) null);
        env.getBody().setMethod(method);
        super.serialize(env);

        if (recipients.isEmpty()) {
            throw new APIException("no-recipients-specified");
        }

        SOAPParameter recipients = method.addParameter(SOAPConsts.MM7RecipientsParameterName);
        buildAddressList(recipients);

        if (serviceCode != null && serviceCode.length() > 0) {
            method.addParameter(SOAPConsts.MM7ServiceCodeParameterName).setValue(serviceCode);
        }

        if (linkedID != null && linkedID.length() > 0) {
            method.addParameter(SOAPConsts.MM7LinkedIDParameterName).setValue(linkedID);
        }

        if (messageClass != null) {
            method.addParameter(SOAPConsts.MM7MessageClassParameterName).setValue(messageClass.toString());
        }

        // TimeStamp should be here
        if (replyChargingSize > 0 || (replyDeadline != null && replyDeadline.length() > 0)) {
            SOAPParameter replyCharging = method.addParameter(SOAPConsts.MM7ReplyChargingParameterName);
            if(replyChargingSize > 0) {
                replyCharging.addAttribute(SOAPConsts.MM7ReplyChargingSizeAttributeName, String.valueOf(replyChargingSize));
            }
            if (replyDeadline != null && replyDeadline.length() > 0) {
                replyCharging.addAttribute(SOAPConsts.MM7ReplyDeadlineAttributeName, replyDeadline);
            }
        }

        if (earliestDeliveryTime != null && earliestDeliveryTime.length() > 0)
            method.addParameter(SOAPConsts.MM7EarliestDeliveryTimeParameterName).setValue(earliestDeliveryTime);

        if (expiry != null && expiry.length() > 0)
            method.addParameter(SOAPConsts.MM7ExpiryDateParameterName).setValue(expiry);

        if (deliveryReport != null) {
            method.addParameter(SOAPConsts.MM7DeliveryReportParameterName).setValue(deliveryReport.toString());
        }

        if (readReply != null) {
            method.addParameter(SOAPConsts.MM7ReadReplyParameterName).setValue(readReply.toString());
        }

        if (priority != null) {
            method.addParameter(SOAPConsts.MM7PriorityParameterName).setValue(priority.toString());
        }

        if (subject != null && subject.length() > 0)
            method.addParameter(SOAPConsts.MM7SubjectParameterName).setValue(subject);

        if (chargedParty != null) {
            method.addParameter(SOAPConsts.MM7ChargedPartyParameterName).setValue(chargedParty.toString());
        }

        if (distributionIndicator != null) {
            if (mm7Namespace.equals(Namespace.REL_5_MM7_1_0.toString())) {
                method.addParameter(SOAPConsts.MM7DistributionProtectionParameterName).setValue(distributionIndicator.toString());
            }else{
                method.addParameter(SOAPConsts.MM7DistributionIndicatorParameterName).setValue(distributionIndicator.toString());
            }
        }

        if (content != null) {
            SOAPParameter contentParam = method.addParameter(SOAPConsts.MM7ContentParameterName);
            cid = Utils.generateContentID();
            contentParam.addAttribute(SOAPConsts.MM7ContentHrefAttributeName, "cid:" + cid);
        }

        // make the mime multipart/related mm7 document
        String envStr = env.toString();
        try {
            MimeMultipart swa = new MimeMultipart("related");
            MimeBodyPart soapPart = new MimeBodyPart();
            soapPart.setContent(envStr, "text/xml");
            swa.addBodyPart(soapPart);
            addContent(swa, cid);

            MimeMessage mess = new MimeMessage(Session.getDefaultInstance(new Properties()));
            mess.setContent(swa);
            mess.saveChanges();

            String[] contentType = mess.getHeader("content-type");
            
            // The default writeTo method doesnt write ContentLength header
            // which is required by anacapa. There is a getSize method which doesnt
            // seem to be able to calculate the size(returns -1). So we have to go thru this exercise
            // of removing all the headers, writing the message to a bytearrayoutputstream and
            // calculating its size
            
            Enumeration headers = mess.getAllHeaders();
            int i = 0;
            while (headers.hasMoreElements()) {
                Header header = (Header) headers.nextElement();
                mess.removeHeader(header.getName());
            }
            
            ByteArrayOutputStream baos = new ByteArrayOutputStream(4096);
            mess.writeTo(baos);
            
            outputStream.write(("Content-Length: " + (baos.size()-2) + "\r\n" ).getBytes());
            outputStream.write(("Content-Type: " + contentType[0] + "\r\n" ).getBytes());
            baos.writeTo(outputStream);

            if (logger.isDebugEnabled()) {
                logger.debug("Content-Length: " + (baos.size()-2) + "\r\n" );
                logger.debug("Content-Type: " + contentType[0] + "\r\n" );
                logger.debug(baos.toString());
            }
            
        }catch(MessagingException me) {
            if (logger.isEnabledFor(Level.WARN)) {
                logger.warn(me.getMessage(), me);
            }
            throw new APIException("messaging-exception-composing", me.getMessage());
            
        }catch(IOException ie) {
            if (logger.isEnabledFor(Level.WARN)) {
                logger.warn(ie.getMessage(), ie);
            }
            throw new APIException("io-exception-writing-to-socket", ie.getMessage());
        }
    }

    private void addContent( MimeMultipart swa, String cid )
                             throws MessagingException,
                                    ContentException {
        if( content != null ) {

            // content object can either be a MimeBodyPart or MultimediaContent only
            // this is governed by setContent
            if( content instanceof MimeBodyPart ) {

                MimeBodyPart mm = ( MimeBodyPart ) content;
                mm.setHeader( "Content-ID", "<" + cid + ">" );
                swa.addBodyPart( mm );

            } else if( content instanceof MultimediaContent ) {

                // even if it is a MultimediaContent object, the getContent from
                // MultimediaContent could return a MimeBodyPart or a MimeMultipart
                // depending on how it was created.
                MultimediaContent mmContent = ( MultimediaContent ) content;
                Object contentFromMC = mmContent.getContent();

                if( contentFromMC instanceof MimeBodyPart ) {

                    MimeBodyPart mm = ( MimeBodyPart ) contentFromMC;
                    mm.setHeader( "Content-ID", "<" + cid + ">" );
                    swa.addBodyPart( mm );

                } else if( contentFromMC instanceof MimeMultipart ) {

                    MimeMultipart multipartContent = ( MimeMultipart ) mmContent.getContent();
                    MimeBodyPart mm = new MimeBodyPart();
                    mm.setHeader( "Content-ID", "<" + cid + ">" );
                    mm.setContent( multipartContent );
                    String start = getSmilContentID( multipartContent );
                    if( start != null ) {
                        String contentType = multipartContent.getContentType();
                        contentType =  contentType + ";\r\n\tstart=\"" + start + "\"" +
                                             ";\r\n\ttype=\"application/smil\"";
                        mm.setHeader( "Content-Type", contentType );
                    }
                    swa.addBodyPart( mm );
                }
            }
        }
    }

    private void buildAddressList( SOAPParameter rcpts )
                                   throws SOAPException {
        for( int i = 0; i < recipients.size(); i++ ) {
            Recipient recipient = ( Recipient ) recipients.get( i );

            SOAPParameter type = rcpts.getParameter( recipient.getType().toString() );
            if( type == null ) {
                type = new SOAPParameter( recipient.getType().toString() );
                rcpts.addParameter( type );
            }

            SOAPParameter addressType = new SOAPParameter( recipient.getAddressType().toString() );
            addressType.setValue( recipient.getAddress() );
            type.addParameter( addressType );
        }
    }

    private static String getSmilContentID( MimeBodyPart[] content )
                                            throws MessagingException {
        for( int i = 0; i < content.length; i++ ) {
            if( content[i].getDataHandler().getContentType().startsWith( "application/smil" ) ) {
                return content[i].getContentID();
            }
        }

        return null;
    }

    private static String getSmilContentID( MimeMultipart multipart )
                                            throws MessagingException {

        for( int i = 0; i < multipart.getCount(); i++ ) {

            MimeBodyPart part = ( MimeBodyPart ) multipart.getBodyPart( i );
            if( part.getContentType().startsWith( "application/smil" ) ) {
                return part.getContentID();
            }
        }

        return null;
    }

    private static final Logger logger = Logger.getLogger( SubmitRequest.class );

    private Vector recipients;
    private String serviceCode;
    private String linkedID;
    private MessageClass messageClass;
    private String date;
    private int replyChargingSize;
    private String replyDeadline;
    private String earliestDeliveryTime;
    private String expiry;
    private Boolean deliveryReport;
    private Boolean readReply;
    private Priority priority;
    private String subject;
    private ChargedParty chargedParty;
    private Boolean distributionIndicator;
    private Object content;
    private String cid;

}
