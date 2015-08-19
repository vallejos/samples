package com.openwave.mms.mm7;

import java.io.IOException;
import java.io.InputStream;

import com.openwave.mms.mm7.soap.SOAPConsts;
import com.openwave.mms.mm7.soap.SOAPException;
import com.openwave.mms.mm7.soap.SOAPHeader;
import com.openwave.mms.mm7.soap.SOAPMethod;
import com.openwave.mms.mm7.soap.SOAPParser;
import com.openwave.mms.mm7.DeviceProfileResponse;

/**
 *  Package-private class which creates the Response object correspoding to
 *  incoming SOAP Response.
 */
class ResponseFactory {

    /**
     *  Static factory method which reads the input stream and creates
     *  the Response object corresponding to the incoming SOAP Response.
     *
     *  @param inputStream the input stream to read the response from.
     *  @throws APIException if the SOAP response cannot be parsed.
     *  @throws IOException if there is an error reading the input stream.
     *  @throws SOAPException if there is an exception retrieving the
     *          envelope, body or method from the soap envelope.
     */
    public static Response makeResponse( InputStream inputStream )
                                         throws APIException,
                                                IOException,
                                                SOAPException {
        SOAPParser parser = new SOAPParser( inputStream );

        SOAPHeader header = parser.getEnvelope().getHeader();
        SOAPMethod method = parser.getEnvelope().getBody().getMethod();
    
        if( method.getName().equals( SOAPConsts.MM7SubmitResMethodName ) ) {
            return new SubmitResponse( header, method );
        } /*else if( method.getName().equals( SOAPConsts.MM7CancelResMethodName ) ) {
            return new CancelResponse( header, method );
        } else if( method.getName().equals( SOAPConsts.MM7ReplaceResMethodName ) ) {
            return new ReplaceResponse( header, method );
        }*/ else if( method.getName().equals( SOAPConsts.SOAPFault ) ) {
            return new FaultResponse( method );
        } else if( method.getName().equals( SOAPConsts.MM7GetDeviceProfileResMethodName ) ) {
            return new DeviceProfileResponse( header, method );
        } else throw new APIException( ErrorCode.VALIDATION_ERROR,
                                       "unknown-response-method-name", method.getName() );
    }

}
