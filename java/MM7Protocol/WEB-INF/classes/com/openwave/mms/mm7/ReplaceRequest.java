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
import com.openwave.mms.mm7.util.Utils;
import com.openwave.mms.mm7.util.Enum;

/**
 * This class encapsulates an MM7 ReplaceRequest message that contains multimedia 
 * content and which the application sends to MMSC for delivery to mobile subscribers. 
 * <p>
 * To use this class:
 * <OL>
 * <LI> Create a <code>ReplaceRequest</code> object.
 * <LI> Set additional data elements that you require.
 * <LI> Create an array of <code>javax.mail.internet.MimeBodyPart</code> objects,
 *      using standard JavaMail classes, that define the multimedia message content
 *      and assign it to the <code>ReplaceRequest</code> object.
 * <LI> Use the {@link RelayConnection} class' <code>sendRequest</code> method to send the
 *      submit request to the carrier.
 * </OL>
 * </p>
 * For further information about using this class to create and send submit requests
 * to the carrier, see the <em>Openwave MMS Library Developer's Guide</em>.
 */ 
public final class ReplaceRequest extends Request {

    /**
     *  Instantiates a <code>ReplaceRequest</code> object.
     */
    public ReplaceRequest() {
        readReply = false;
        //allowAdaptations = true;
    }

    /**
     *  Sets the message id for this message.
     *  <p>
     *  The message id should match the message id to be replaced in the MMSC.
     *  The original message id was returned from the MMSC in the submit 
     *  response message.
     *
     *  @param messageID The ID of the message to be replaced in the relay.
     */
    public void setMessageID( String messageID ) { this.messageID = messageID; }

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

    /*
     *  Sets the earliest delivery time for a deferred delivery
     *  message. The relay will deliver the message only after this time
     *  has passed.
     *
     *  @param earliestDeliveryTime The time after which the message can be delivered.
     */
    //public void setEarliestDeliveryTime( Calendar earliestDeliveryTime ) {
        //this.earliestDeliveryTime = Utils.convertDateToISO8601( earliestDeliveryTime );
    //}


    /**
     *  Sets whether a read-reply report is requested for this message. If one is requested, 
     *  the recipient user agent generates one after downloading the message and
     *  the carrier delivers the report to the sender.
     *
     *  @param readReply A Boolean value that specifies whether a read-reply report is requested. 
     *                         <code>true</code> indicates that a report is requested.
     *                         The default is <code>false</code>, a report is not requested. 
     */
    public void setReadReply( boolean readReply ) { this.readReply = readReply; }

    /*
     *  Sets whether recipients can redistribute this message.
     *
     *  @param distributionIndicator A boolean value that specifies whether recipients can redistribute
     *         this message. <code>true</code> indicates that recipients can redistribute this 
     *         message. The default is <code>false</code>, recipients cannot redistribute this 
     *         message.
     */
    //public void setDistributionIndicator( boolean distributionIndicator ) {
        //this.distributionIndicator = distributionIndicator;
    //}

    /**
     *  Sets the multimedia message content of this message. 
     *  @param content The multimedia content of this message as an array of 
     *         <code>javax.mail.internet.MimeBodyPart</code> objects created using
     *         standard JavaMail classes.
     */
    public void setContent( MimeBodyPart[] content ) {
        this.content = content;
    }

    /**
     *  Protected method that the {@link Request} class uses to write this message 
     *  to an <code>OutputStream</code> object. This method is for API internal use only.
     *
     *  @param outputStream The <code>OutputStream</code> object to which this object is written.
     *  @throws APIException If the API encounters an error while composing the message from MIME 
     *          body parts or while writing the message to the output stream.
     *  @throws SOAPException If any of the SOAP Envelope creation rules are violated.
     *
     */
    protected void writeTo( OutputStream outputStream ) 
                            throws APIException, SOAPException {
        SOAPEnvelope env = new SOAPEnvelope( null );
        SOAPMethod method = new SOAPMethod( SOAPConsts.MM7ReplaceReqMethodName );
        env.getBody().setMethod( method );
        super.serialize( env );

        if( messageID == null ) {
            throw new APIException("no-message-id-specified");
        }

        if( serviceCode != null && serviceCode.length() > 0 ) {
            method.addParameter( SOAPConsts.MM7ServiceCodeParameterName )
                  .setValue( serviceCode );
        }

        //if( earliestDeliveryTime != null && 
            //earliestDeliveryTime.length() > 0 )
            //method.addParameter( SOAPConsts.MM7EarliestDeliveryTimeParameterName )
                  //.setValue( earliestDeliveryTime );

        if( content != null && content.length > 0 ) {
            SOAPParameter content = method.addParameter( SOAPConsts.MM7ContentParameterName );
            cid = Utils.generateContentID();
            content.addAttribute( SOAPConsts.MM7ContentHrefAttributeName,
                                  "cid:" + cid );
        }

        method.addParameter( SOAPConsts.MM7ReadReplyParameterName )
              .setValue( String.valueOf( readReply ) );

        //method.addParameter( SOAPConsts.MM7DistributionIndicatorParameterName )
              //.setValue( String.valueOf( distributionIndicator ) );

        // make the mime multipart/related mm7 document
        String envStr = env.toString();
        try {
            MimeMultipart swa = new MimeMultipart( "related" );
            MimeBodyPart soapPart = new MimeBodyPart();
            soapPart.setContent( envStr, "text/xml" );
            swa.addBodyPart( soapPart );
            if( content != null ) {
                MimeBodyPart mm = null;
                if( content.length == 1 ) {
                    // if only one part in content add it as is
                    mm = content[0];
                    mm.setHeader( "content-id", "<" + cid + ">" );
                } else {
                    // if multiple parts in content, construct a multipart and add it
                    // per 3GPP 23.140
                    MimeMultipart mmMultipart = new MimeMultipart();
                    for( int i = 0; i < content.length; i++ ) {
                        mmMultipart.addBodyPart( content[i] );
                    }
                    mm = new MimeBodyPart();
                    mm.addHeader( "content-id", "<" + cid + ">" );
                    mm.setContent( mmMultipart );
                } 
                swa.addBodyPart( mm );
            }
            MimeMessage mess = new MimeMessage( Session.getDefaultInstance(
                                                new Properties() ) );
            mess.setContent( swa );
            mess.saveChanges();

            String[] contentType = mess.getHeader( "content-type" );
            // hack to remove folding of headers. once in every 500 transactions or so
            // anacapa seems to go into an infinite loop and timeout when processing folded
            // headers especially when the content type header is the only thing in the tcp
            // that it is processing.
            contentType[0] = contentType[0].replace( '\r', ' ' );
            contentType[0] = contentType[0].replace( '\n', ' ' );

            // The default writeTo method doesnt write ContentLength header
            // which is required by anacapa. There is a getSize method which doesnt
            // seem to be able to calculate the size(returns -1). So we have to go thru this exercise
            // of removing all the headers, writing the message to a bytearrayoutputstream and
            // calculating its size
            Enumeration headers = mess.getAllHeaders();
            int i = 0;
            while( headers.hasMoreElements() ) {
                Header header = ( Header ) headers.nextElement();
                mess.removeHeader( header.getName() );
            }
            ByteArrayOutputStream baos = new ByteArrayOutputStream( 4096 );
            mess.writeTo( baos );
            outputStream.write( ("Content-Length: " + (baos.size()-2) + "\r\n" ).getBytes() );
            outputStream.write( ("Content-Type: " + contentType[0] + "\r\n" ).getBytes() );
            baos.writeTo( outputStream );

            if( logger.isDebugEnabled() ) {
                logger.debug( "Content-Length: " + (baos.size()-2) + "\\r\\n" );
                logger.debug( "Content-Type: " + contentType[0] + "\\r\\n" );
                if( baos.size() <= 4096 )
                    logger.debug( baos.toString() );
                else logger.debug( "\n<< message size > 4K - not logged >>\n" );
                // too bad i cant get part of it if it is too big
            }
        } catch( MessagingException me ) {
            if( logger.isEnabledFor( Level.WARN ) ) {
                logger.warn( me.getMessage(), me );
            }
            throw new APIException( "messaging-exception-composing",
                                    me.getMessage() );
        } catch( IOException ie ) {
            if( logger.isEnabledFor( Level.WARN ) ) {
                logger.warn( ie.getMessage(), ie );
            }
            throw new APIException( "io-exception-writing-to-socket",
                                    ie.getMessage() );
        }
    }

    private static final Logger logger = Logger.getLogger( SubmitRequest.class );

    private String messageID;
    private String serviceCode;
    private String date;
    //private String earliestDeliveryTime;
    private boolean readReply;
    //private boolean allowAdaptations;
    //private boolean distributionIndicator;
    private MimeBodyPart[] content;
    private String cid;

}
