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
 * This class encapsulates an MM7 CancelRequest message that contains multimedia 
 * content and which the application sends to MMSC for delivery to mobile subscribers. 
 * <p>
 * To use this class:
 * <OL>
 * <LI> Create a <code>CancelRequest</code> object.
 * <LI> Set additional data elements that you require.
 * <LI> Use the {@link RelayConnection} class' <code>sendRequest</code> method to send the
 *      cancel request to the carrier.
 * </OL>
 * </p>
 * For further information about using this class to create and send submit requests
 * to the carrier, see the <em>Openwave MMS Library Developer's Guide</em>.
 */ 
public final class CancelRequest extends Request {

    /**
     *  Instantiates a <code>CancelRequest</code> object.
     */
    public CancelRequest() {
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
        SOAPMethod method = new SOAPMethod( SOAPConsts.MM7CancelReqMethodName );
        env.getBody().setMethod( method );
        super.serialize( env );

        if( messageID == null ) {
            throw new APIException("no-message-id-specified");
        }

        String envStr = env.toString();
        try {
            MimeMessage mess = new MimeMessage( Session.getDefaultInstance (
                                                   new Properties() ) );
            mess.setContent( envStr, "text/xml" );
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

}
