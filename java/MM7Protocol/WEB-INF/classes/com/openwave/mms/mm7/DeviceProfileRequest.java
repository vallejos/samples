package com.openwave.mms.mm7;

import java.io.ByteArrayOutputStream;
import java.util.Enumeration;
import java.io.IOException;
import java.io.OutputStream;
import java.util.Properties;
import java.util.Vector;
import java.util.Iterator;

import javax.mail.Header;
import javax.mail.MessagingException;
import javax.mail.internet.MimeMessage;
import javax.mail.Session;

import org.apache.log4j.Logger;
import org.apache.log4j.Level;

import com.openwave.mms.mm7.soap.SOAPConsts;
import com.openwave.mms.mm7.soap.SOAPEnvelope;
import com.openwave.mms.mm7.soap.SOAPException;
import com.openwave.mms.mm7.soap.SOAPMethod;
import com.openwave.mms.mm7.soap.SOAPQName;

/**
 * This class encapsulates an MM7 DeviceProfileRequest message that contains multimedia 
 * content which the application sends to MMSC for delivery to mobile subscribers. 
 * <p>
 * To use this class:
 * <OL>
 * <LI> Create a <code>DeviceProfileRequest</code> object.
 * <LI> Set additional data elements that you require.
 * <LI> Use the <code>sendRequest</code> method of the {@link RelayConnection} class 
 *      to send the cancel request to the carrier.
 * </OL>
 * </p>
 * For further information about using this class to create and send submit requests
 * to the carrier, see the <em>Openwave MMS Library Developer's Guide</em>.
 */ 
public final class DeviceProfileRequest extends Request {

    /**
     *  Instantiates a <code>DeviceProfileRequest</code> object.
     */
    public DeviceProfileRequest() {
        numbers = new Vector();
    }

    /**
     *  Adds a phone number to the request.
     *
     *  @param phoneNumber The ID of the message to be replaced in the MMS Relay.
     */
    public void addNumber( String phoneNumber ) { 
        if ( phoneNumber != null )
            numbers.add( phoneNumber ); 
    }

    /**
     *  Adds a phone number to the request.
     *
     *  @param numbers The vector of phone numbers to be added.
     */
    public void addNumbers( Vector numbers ) { 
        if ( numbers != null )
            this.numbers.addAll( numbers ); 
    }

    /**
     *  Protected method that the {@link Request} class uses to write the message 
     *  to an <code>OutputStream</code> object. This method is for API internal use only.
     *
     *  @param outputStream The <code>OutputStream</code> object to which the object is written.
     *  @throws APIException The API encounters an error while composing the message from MIME 
     *          body parts or while writing the message to the output stream.
     *  @throws SOAPException The SOAP Envelope creation rules are violated.
     *
     */
    protected void writeTo( OutputStream outputStream ) 
                            throws APIException, SOAPException {
        SOAPEnvelope env = new SOAPEnvelope( null );
        SOAPMethod method = new SOAPMethod( new SOAPQName( SOAPConsts.MM7GetDeviceProfileReqMethodName, "opwvmm7", SOAPConsts.OPWVNamespace ), (Vector) null );
        env.getBody().setMethod( method );
        super.serialize( env );

        if( numbers.size() == 0 ) {
            throw new APIException("no-phone-numbers-specified");
        }

        Iterator numbersIter = numbers.iterator();
        while ( numbersIter.hasNext() ) {
            method.addParameter( SOAPConsts.MM7UserParameterName, 
                                 ( String ) numbersIter.next() );
        }

        String envStr = env.toString();
        try {
            MimeMessage mess = new MimeMessage( Session.getDefaultInstance (
                                                   new Properties() ) );
            mess.setContent( envStr, "text/xml" );
            mess.saveChanges();

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
            outputStream.write( ("Content-Type: text/xml\r\n" ).getBytes() );
            baos.writeTo( outputStream );

            if( logger.isDebugEnabled() ) {
                logger.debug( "Content-Length: " + (baos.size()-2) + "\r\n" );
                logger.debug( "Content-Type: text/xml\r\n" );
                logger.debug( baos.toString() );
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

    private static final Logger logger = Logger.getLogger( DeviceProfileRequest.class );

    private Vector numbers;

}
