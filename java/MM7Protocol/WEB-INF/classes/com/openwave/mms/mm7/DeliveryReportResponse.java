package com.openwave.mms.mm7;

import java.util.HashMap;
import java.util.Vector;
import java.io.IOException;
import java.io.OutputStream;

import com.openwave.mms.mm7.soap.SOAPBody;
import com.openwave.mms.mm7.soap.SOAPConsts;
import com.openwave.mms.mm7.soap.SOAPEnvelope;
import com.openwave.mms.mm7.soap.SOAPException;
import com.openwave.mms.mm7.soap.SOAPHeader;
import com.openwave.mms.mm7.soap.SOAPMethod;
import com.openwave.mms.mm7.soap.SOAPParameter;
import com.openwave.mms.mm7.soap.SOAPQName;

/**
 *  This class extends the {@link Response} object to encapsulate the MM7 
 *  DeliveryReportResponse message that the application sends to MMSC in 
 *  response to a valid DeliveryReport request ({@link DeliveryReport}). 
 *  <p> 
 *  Create and use <code>DeliveryReportResponse</code> objects in the 
 *  <code>processDeliveryReport</code> method of a custom class that
 *  either implements the {@link MessageListener} interface or extends the 
 *  {@link MessageListenerAdapter} class. 
 *  <p>
 *  To use this class, follow these guidelines:
 * <OL>
 * <LI> Create a <code>DeliveryReportResponse</code> object.
 * <LI> Set the mandatory <code>StatusCode</code> field, using the status codes  
 *      from the {@link ErrorCode} class.
 * <LI> Set additional data elements that you require.
 * <LI> Use the <code>DeliveryReportResponse</code> object as the return value of the 
 *      <code>processDeliveryReport</code> method. The API converts it to a SOAP
 *      message and sends it to MMSC.
 * </OL>
 * </p>
 * For further information about using this class to return deliver responses to
 * MMSC, see the <em>Openwave MMS Library Developer's Guide</em>.
 */ 
public final class DeliveryReportResponse extends Response {

    /**
     *  Instantiates the <code>DeliveryReportResponse</code> object.
     */
    public DeliveryReportResponse() {}

    /**
     *  Sets the status of the delivery report request to which this object is the response.
     *
     *  @param statusCode The error code of this response. Must be an {@link ErrorCode}
     *  object.
     */
    public void  setStatusCode( ErrorCode statusCode ) {
        this.statusCode = statusCode;
    }

    /**
     *  Sets the text description associated with the status code of the delivery report 
     *  request for which this object is the response.
     *
     *  @param statusText The status text of this delivery report request.
     */
    public void  setStatusText( String statusText ) {
        this.statusText = statusText;
    }

    /**
     *  Protected method that the {@link Response} class uses to marshall this object 
     *  into a SOAP Envelope. This method is for API internal use only.
     *
     *  @throws SOAPException If any SOAP Envelope formation rules are violated while writing 
     *          the object.
     *
     */
    protected SOAPEnvelope serialize() throws SOAPException {
        SOAPEnvelope env = new SOAPEnvelope( null );
        SOAPHeader header = new SOAPHeader();
        header.addHeader( namespace,
                          SOAPConsts.MM7TransactionIDParameterName,
                          transactionID );

        env.setHeader( header );
   
        SOAPBody body = new SOAPBody();
        SOAPMethod method = new SOAPMethod( new SOAPQName( SOAPConsts.MM7DeliveryReportResMethodName, "mm7", namespace ), ( Vector ) null );

        method.addParameter( SOAPConsts.MM7MM7VersionParameterName,
                             mm7Version );

        SOAPParameter status = method.addParameter( SOAPConsts.MM7StatusParameterName );
        if( statusText != null && statusText.length() > 0 ) {
            status.addParameter( SOAPConsts.MM7StatusTextParameterName,
                                 statusText );
        }

        status.addParameter( SOAPConsts.MM7StatusCodeParameterName,
                             String.valueOf( statusCode.getCode() ) );
        body.setMethod( method );
        env.setBody( body );
        return env;
    }

}
