package com.openwave.mms.mm7;

import java.io.IOException;
import java.io.OutputStream;
import java.util.HashMap;
import java.util.Vector;

import com.openwave.mms.mm7.soap.SOAPConsts;
import com.openwave.mms.mm7.soap.SOAPBody;
import com.openwave.mms.mm7.soap.SOAPEnvelope;
import com.openwave.mms.mm7.soap.SOAPException;
import com.openwave.mms.mm7.soap.SOAPMethod;
import com.openwave.mms.mm7.soap.SOAPParameter;
import com.openwave.mms.mm7.soap.SOAPQName;

/**
 *  This class encapsulates a SOAP fault that the application receives from
 *  or returns to MMSC in response to an error processing a request. 
 *  Specifically, <code>FaultResponse</code> objects are:
 *  <UL>
 *  <LI> Returned in reponse to an MMSC deliver request, delivery report,  
 *       or read-reply request that the application cannot process successfully
 *  <LI> Received in reponse to a submit request that MMSC cannot 
 *       process successfully
 *  </UL>
 *
 *  If an error occurs while processing requests from MMSC in a site-specific 
 *  implementation of the <code>processDeliverRequest</code>,
 *  <code>processDeliveryReport</code>, or <code>processReadReply</code> method, 
 *  do one of the following:
 *  <UL>
 *  <LI> Create a <code>FaultResponse</code> object and use it as the return value of the 
 *       request-specific <code>process</code> method 
 *  <LI> Throw a {@link MessageProcessingException}, to have the API automatically create 
 *       the <code>FaultResponse</code>
 *  </UL>
 *
 *  If an error occurs while the MMSC processes a submit request from the application,
 *  it returns a SOAP fault that the API converts to a <code>FaultResponse</code> object
 *  for processing. To handle the error response, use the <code>FaultResponse</code>
 *  accessor methods to get the data and examine the error.
 *  <p>
 *  For further information about using this class to return fault responses to and
 *  receive and process them from MMSC, see the <em>Openwave MMS Library Developer's 
 *  Guide</em>.</p>
 */ 
public final class FaultResponse extends Response {

    /**
     *  Instantiates a <code>FaultResponse</code> object with error status and SOAP 
     *  fault information. The API encapsulates this object into a SOAP fault and 
     *  sends it to MMSC.
     *
     *  @param faultCode The code that identifies the SOAP <code>faultcode</code>
     *           associated with the error.
     *  @param faultString The text that identifies the SOAP <code>faultstring</code>
     *           associated with the error.
     *  @param statusText The text that describes the error status and identifies the
     *           SOAP fault <code>Detail</code> element <code>StatusText</code>. 
     *  @param statusCode The status code for the error and identifies the SOAP fault
     *           <code>Detail</code> element <code>StatusText</code>. Must be an
     *           {@link ErrorCode} object.
     */
    public FaultResponse( String faultCode,
                          String faultString,
                          String statusText,
                          ErrorCode statusCode ) {
        this.statusText = statusText;
        this.statusCode = statusCode;
        this.faultCode = faultCode;
        this.faultString = faultString;
    }

    /**
     *  Instantiates a <code>FaultResponse</code> object with error status information. 
     *  The API supplies a default fault string and fault code before encapsulating
     *  this object into a SOAP fault and sending it to MMSC.
     *
     *  @param statusText The text that describes the error status and identifies the
     *           SOAP fault <code>Detail</code> element <code>StatusText</code>. 
     *  @param statusCode The status code for the error and identifies the SOAP fault
     *           <code>Detail</code> element <code>StatusText</code>. Must be an
     *           {@link ErrorCode} object.
     */
    public FaultResponse( String statusText, ErrorCode statusCode ) {
        this.statusText = statusText;
        this.statusCode = statusCode;
        faultCode = "Server";
        faultString = "Unknown Server Error";
    }

    /**
     *  Package-private constructor used by the sendRequest method in RelayConnection when
     *  it sees a SOAP Fault in the reply sent by the relay.
     *
     *  @param fault The SOAPMethod object representing the fault.
     *  @throws APIException thrown when the SOAP packet creation rules are violated.
     *  @throws SOAPException if values cannot be retrieved.
     *
     */
    FaultResponse( SOAPMethod fault ) throws SOAPException,
                                             APIException {
        faultCode = fault.getValue( SOAPConsts.MM7FaultCodeParameterName );
        faultString = fault.getValue( SOAPConsts.MM7FaultStringParameterName );
        SOAPParameter detail = fault.getParameter( SOAPConsts.MM7DetailParameterName );
        if( detail != null ) {
            SOAPParameter rsErrorRsp = detail.getParameter( SOAPConsts.MM7RSErrorRspMethodName );
            if( rsErrorRsp == null ) {
                throw new APIException( ErrorCode.SERVER_ERROR,
                                        "no-error-rsp" );
            }

            SOAPParameter status = rsErrorRsp.getParameter( SOAPConsts.MM7StatusParameterName );
            if( status == null ) {
                throw new APIException( ErrorCode.SERVER_ERROR,
                                        "no-status-in-error-rsp" );
            }

            statusText = status.getValue( SOAPConsts.MM7StatusTextParameterName );
            this.detail = status.getValue( SOAPConsts.MM7DetailsParameterName );

            String statusCodeStr = status.getValue( SOAPConsts.MM7StatusCodeParameterName );
            if( statusCodeStr == null || statusCodeStr.length() == 0 ) {
                throw new APIException( ErrorCode.SERVER_ERROR,
                                        "no-status-code" );
            }
            try {
                statusCode = ErrorCode.getErrorCodeByCode( Integer.parseInt( statusCodeStr ) );
            } catch( NumberFormatException nfe ) {
                throw new APIException( ErrorCode.SERVER_ERROR,
                                        "error-code-nan",
                                        nfe.getMessage() );
            }
        }
    }

    /**
     *  Gets the <code>faultstring</code> element of this SOAP fault response. 
     *
     *  @return The SOAP fault text string of this fault response.
     */
    public String getFaultString() { return faultString; }

    /**
     *  Gets the <code>detail</code> element of this SOAP fault response. 
     *
     *  @return The SOAP detail text string of this fault response.
     */
    public String getDetail() { return detail; }

    /**
     *  Gets the <code>faultcode</code> element of this SOAP fault response.
     *
     *  @return The SOAP fault code of this fault response.
     */
    public String getFaultCode() { return faultCode; }

    /**
     *  Protected method that the {@link Response} class uses to marshall  
     *  this fault response to an SOAP Envelope. This 
     *  method is for API internal use only.
     *
     *  @throws SOAPException If any SOAP Envelope formation rules are  
     *          violated while writing the object.
     *
     */
    protected SOAPEnvelope serialize() throws SOAPException {
        HashMap namespaces = new HashMap();
        namespaces.put( SOAPConsts.SOAPMM7Prefix,
                        namespace );
        SOAPEnvelope env = new SOAPEnvelope( namespaces );

        SOAPBody body = new SOAPBody();
        SOAPMethod method = new SOAPMethod( SOAPConsts.SOAPFault );
        method.addParameter( SOAPConsts.MM7FaultStringParameterName,
                             faultString );
        method.addParameter( SOAPConsts.MM7FaultCodeParameterName,
                             faultCode );
        SOAPParameter detail = new SOAPParameter( SOAPConsts.MM7DetailParameterName );
        SOAPParameter vaspErrorRsp = new SOAPParameter( 
                                         new SOAPQName( 
                                             SOAPConsts.MM7VASPErrorRspMethodName,
                                             "", namespace ),
                                         null,
                                         ( Vector ) null );
        vaspErrorRsp.addParameter( SOAPConsts.MM7MM7VersionParameterName,
                                   mm7Version );

        SOAPParameter status = vaspErrorRsp.addParameter( SOAPConsts.MM7StatusParameterName );
        status.addParameter( SOAPConsts.MM7StatusTextParameterName,
                             statusText );
        status.addParameter( SOAPConsts.MM7StatusCodeParameterName,
                             String.valueOf( statusCode.getCode() ) );
        detail.addParameter( vaspErrorRsp );
        method.addParameter( detail );
        body.setMethod( method );
        env.setBody( body );
        return env;
    }

    private String faultString;
    private String faultCode;
    private String detail;

}
