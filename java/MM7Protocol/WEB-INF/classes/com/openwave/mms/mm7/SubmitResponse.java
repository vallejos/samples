package com.openwave.mms.mm7;

import java.io.IOException;
import java.io.OutputStream;

import com.openwave.mms.mm7.soap.SOAPConsts;
import com.openwave.mms.mm7.soap.SOAPEnvelope;
import com.openwave.mms.mm7.soap.SOAPException;
import com.openwave.mms.mm7.soap.SOAPHeader;
import com.openwave.mms.mm7.soap.SOAPMethod;
import com.openwave.mms.mm7.soap.SOAPParameter;

/**
 *  This class extends the {@link Response} object to encapsulate the MM7 
 *  SubmitResponse message that MMSC sends to the application in response
 *  to a vaild SubmitRequest message {@link SubmitRequest}. 
 *  <p>
 *  When the API receives a SubmitResponse from MMSC, it converts the SOAP
 *  response to a <code>SubmitResponse</code> object and passes it back as 
 *  the return value of the <code>sendRequest</code> method of the 
 *  {@link RelayConnection} class that submitted the request. To handle the 
 *  response, use the accessors to get the status data and examine the
 *  submit request results. 
 */
public final class SubmitResponse extends Response {

    /**
     *  Package-private constructor. The SubmitResponse is created by the API when it
     *  receives a response to the SubmitRequest from the relay.
     *
     *  @param header The SOAP header.
     *  @param response The SubmitResponse as a SOAP method object.
     *  @throws SOAPException if there is an error getting element values from the response.
     *  @throws APIException if statuscode is not returned, status code is not a number or
     *          if message id was not returned.
     */
    SubmitResponse( SOAPHeader header,
                    SOAPMethod response ) throws SOAPException,
                                                 APIException {
        transactionID = header.getHeaderValue(
                                SOAPConsts.MM7TransactionIDParameterName );
        mm7Version = response.getValue(
                                SOAPConsts.MM7MM7VersionParameterName );
        SOAPParameter status = response.getParameter(
                                SOAPConsts.MM7StatusParameterName );
        statusText = status.getValue( SOAPConsts.MM7StatusTextParameterName );
        String code = status.getValue( SOAPConsts.MM7StatusCodeParameterName );
        if( code == null || code.length() == 0 ) {
            throw new APIException( ErrorCode.SERVER_ERROR,
                                    "no-status-code" );
        }
        try {
            statusCode = ErrorCode.getErrorCodeByCode( Integer.parseInt( code ) );
        } catch( NumberFormatException nfe ) {
            throw new APIException( ErrorCode.SERVER_ERROR,
                                    "status-code-nan" );
        }
        messageID = response.getValue( SOAPConsts.MM7MessageIDParameterName );
        if( messageID == null || messageID.length() == 0 ) {
            throw new APIException( ErrorCode.SERVER_ERROR,
                                    "message-id-not-returned" );
        }
    }

    /**
     *  Gets the unique MMSC-generated message ID for the submitted mesage.
     *  Use this ID in subsequent requests and reports that relate to this message.
     *
     *  @return The MMSC-generated ID of the message submitted.
     */
    public String getMessageID() { return messageID; }

    /**
     *  Protected method that the {@link Response} class uses to marshall this  
     *  object to a SOAP Envelope. This method is for API internal use only.
     *
     *  @return A null object. SubmitResponse is never sereialized.
     */
    protected SOAPEnvelope serialize( ) { return null; }

    private String messageID;

}
