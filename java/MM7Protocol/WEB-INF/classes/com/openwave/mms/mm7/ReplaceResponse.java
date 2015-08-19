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
 *  ReplaceResponse message that MMSC sends to the application in response
 *  to a vaild ReplaceRequest message {@link SubmitRequest}. 
 *  <p>
 *  When the API receives a ReplaceResponse from MMSC, it converts the SOAP
 *  response to a <code>ReplaceResponse</code> object and passes it back as 
 *  the return value of the <code>sendRequest</code> method of the 
 *  {@link RelayConnection} class that submitted the request. To handle the 
 *  response, use the accessors to get the status data and examine the
 *  submit request results. 
 */
public final class ReplaceResponse extends Response {

    /**
     *  Package-private constructor. The ReplaceResponse is created by the API when it
     *  receives a response to the ReplaceRequest from the relay.
     *
     *  @param header The SOAP header.
     *  @param response The ReplaceResponse as a SOAP method object.
     *  @throws SOAPException if there is an error getting element values from the response.
     *  @throws APIException if statuscode is not returned, status code is not a number or
     *          if message id was not returned.
     */
    ReplaceResponse( SOAPHeader header,
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
    }

    /**
     *  Protected method that the {@link Response} class uses to marshall this  
     *  object to a SOAP Envelope. This method is for API internal use only.
     *
     *  @return A null object. SubmitResponse is never sereialized.
     */
    protected SOAPEnvelope serialize( ) { return null; }

    private String messageID;

}
