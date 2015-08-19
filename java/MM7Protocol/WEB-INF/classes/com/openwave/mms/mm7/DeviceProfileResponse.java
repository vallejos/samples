package com.openwave.mms.mm7;

import java.io.IOException;
import java.io.OutputStream;
import java.util.HashMap;
import java.util.Iterator;

import com.openwave.mms.mm7.soap.SOAPConsts;
import com.openwave.mms.mm7.soap.SOAPEnvelope;
import com.openwave.mms.mm7.soap.SOAPException;
import com.openwave.mms.mm7.soap.SOAPHeader;
import com.openwave.mms.mm7.soap.SOAPMethod;
import com.openwave.mms.mm7.soap.SOAPParameter;

/**
 *  This class extends the {@link Response} object to encapsulate the Openwave
 *  MM7 GetDeviceProfileResponse message that MMSC sends to the application 
 *  in response to a valid GetDeviceProfileRequest message.
 *  <p>
 *  When the API receives a GetDeviceProfileResponse from MMSC, it converts 
 *  the SOAP response to a <code>DeviceProfileResponse</code> object and 
 *  passes it back as the return value of the 
 *  <code>DeviceProfileRequest</code> method of the 
 *  {@link RelayConnection} class that submitted the request. To handle the 
 *  response, use the accessors to get the status data and examine the
 *  submit request results. 
 */
public final class DeviceProfileResponse extends Response {

    /**
     *  Package-private constructor. The DeviceProfileResponse is created by the API when it
     *  receives a response to the DeviceProfileRequest from the MMS Relay.
     *
     *  @param header The SOAP header.
     *  @param response The GetDeviceProfileResponse as a SOAP method object.
     *  @throws SOAPException There is an error getting element values from 
     *  the response.
     *  @throws APIException The status code is not returned or is not a number. 
     */
    DeviceProfileResponse( SOAPHeader header,
                    SOAPMethod response ) throws SOAPException,
                                                 APIException {
        transactionID = header.getHeaderValue(
                                SOAPConsts.MM7TransactionIDParameterName );
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
        userDevices = new HashMap();

        Iterator paramsIter = response.getParameters().iterator();
        while ( paramsIter.hasNext() ) {
            SOAPParameter deviceParam = ( SOAPParameter ) paramsIter.next();
            // Get the next UserDevice node
            if ( deviceParam.getName().equals( SOAPConsts.MM7UserDeviceParameterValue ) ) {
                String deviceInfo = null;
                String deviceUser;
                String deviceStatusText;
                String deviceStatusCode;
                int deviceType = 0;

                SOAPParameter statusParam = deviceParam.getParameter( SOAPConsts.MM7StatusParameterName );
                deviceStatusCode = statusParam.getValue( SOAPConsts.MM7StatusCodeParameterName );
                if ( deviceStatusCode.equals( SOAPConsts.MM7StatusOKParameterValue ) ) {
                    // Got a valid user...Now see if we got a Profile URL or User Agent
                    deviceInfo = deviceParam.getValue( SOAPConsts.MM7ProfileURLParameterValue );
                    if ( deviceInfo == null ) {
                        deviceInfo = deviceParam.getValue( SOAPConsts.MM7UserAgentParameterValue ); 
                        deviceType = UserDevice.USERAGENT;
                    } else {
                        deviceType = UserDevice.PROFILEURL;
                    }
                }

                deviceStatusText = statusParam.getValue( SOAPConsts.MM7StatusTextParameterName );

                ErrorCode errCode = ErrorCode.getErrorCodeByCode(Integer.parseInt(deviceStatusCode));
                deviceUser = deviceParam.getValue( SOAPConsts.MM7UserParameterName );

                // Append to the userDevices vector.
                UserDevice device = new UserDevice( deviceUser, deviceType,
                        deviceInfo, errCode );
                userDevices.put( deviceUser, device );
            }
        }
    }

    public UserDevice getUserDevice( String number ) {
        return (UserDevice)userDevices.get( number );
    }

    /**
     *  Protected method that the {@link Response} class uses to marshal this  
     *  object to a SOAP Envelope. This method is for API internal use only.
     *
     *  @return A null object. SubmitResponse is never serialized.
     */
    protected SOAPEnvelope serialize( ) { return null; }

    private HashMap userDevices;

}
