package com.openwave.mms.mm7;

import java.util.HashMap;
import java.util.Properties;
import java.util.Vector;
import java.io.OutputStream;
import java.io.IOException;

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
 *  DeliverResponse message that the application sends to MMSC in response 
 *  to a valid DeliverRequest message ({@link DeliverRequest}). 
 *  <p> 
 *  Create and use <code>DeliverResponse</code> objects in the 
 *  <code>processDeliverRequest</code> method of a custom class that
 *  either implements the {@link MessageListener} interface or extends the 
 *  {@link MessageListenerAdapter} class. 
 *  <p>
 *  To use this class, follow these guidelines:
 *  <OL>
 *  <LI> Create a <code>DeliverResponse</code> object.
 *  <LI> Set the mandatory <code>StatusCode</code> field, using the status codes  
 *       from the {@link ErrorCode} class.
 *  <LI> Set additional data elements that you require.
 *  <LI> Use the <code>DeliverResponse</code> object as the return value of the 
 *       <code>processDeliverRequest</code> method. The API converts it to a SOAP
 *       message and sends it to MMSC.
 *  </OL>
 *  </p>
 *  For further information about using this class to return deliver responses to
 *  MMSC, see the <em>Openwave MMS Library Developer's Guide</em>.
 */ 
public final class DeliverResponse extends Response {

    /**
     *  Instantiates a <code>DeliverResponse</code> object.
     */
    public DeliverResponse() {}

    /**
     *  Sets content provider-specific billing-related information in this deliver 
     *  response. MMSC stores this value in the <code>vasServiceCode</code>
     *  field of charging data records (CDRs). The service provider can use this value
     *  to aid the process of reconciling copies of provider-related CDRs.
     *
     *  @param serviceCode The service code of this request.
     */
    public void setServiceCode( String serviceCode ) { this.serviceCode = serviceCode; }

    /**
     *  Sets the status of the deliver response.
     *
     *  @param statusCode The error code for this response. Must be an {@link ErrorCode}
     *  object.
     */
    public void setStatusCode( ErrorCode statusCode ) { this.statusCode = statusCode; }

    /**
     *  Sets the text description associated with the status code of the deliver response. 
     *
     *  @param statusText The status text for this deliver response.
     */
    public void  setStatusText( String statusText ) {
        this.statusText = statusText;
    }

    /**
     *  Protected method that the {@link Response} class uses to marshall  
     *  this object into a SOAP envelope. This method is for API internal use only.
     *
     *  @throws SOAPException If any SOAP Envelope formation rules are  
     *          violated while creating the envelope.
     *
     */
    protected SOAPEnvelope serialize() throws SOAPException {
        SOAPEnvelope env = new SOAPEnvelope(null);
        
        Properties props = new Properties();
    	String strdeliveryResponsePrefix = null;
    	String useMustUnderstand = null;
    	
    	try {
			props.load(DeliverResponse.class.getResourceAsStream("/resources/SimpleSender.properties"));
	    	strdeliveryResponsePrefix = props.getProperty("deliveryResponsePrefix");

	    	// Agregar NameSpace o no
	    	String useResponseHeaderNamespace = props.getProperty("useResponseHeaderNamespace");
	    	if (useResponseHeaderNamespace != null) {
	    		if (useResponseHeaderNamespace.equalsIgnoreCase("true"))
	    			namespace = SOAPConsts.MM7Namespaces[SOAPConsts.MM7Namespaces.length - 1];
	    	}
	    	
	    	useMustUnderstand = props.getProperty("useMustUnderstand");
	    	
		} catch (IOException e) {
			e.printStackTrace();
		}

		//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
		// Creacion del cabezal donde ira el Transaccion Id
		//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
		SOAPHeader header = new SOAPHeader();
		//header.addHeader(namespace, SOAPConsts.MM7TransactionIDParameterName, transactionID, strdeliveryResponsePrefix);

		SOAPQName headerQName = new SOAPQName(SOAPConsts.MM7TransactionIDParameterName, strdeliveryResponsePrefix, namespace);
		SOAPParameter param = new SOAPParameter(headerQName, null, (Vector) null);
		param.setValue(transactionID);
		
		if (useMustUnderstand != null) {
			if (useMustUnderstand.equalsIgnoreCase("true"))
				param.addAttribute("mustUnderstand", "1");
		}
		
		header.addHeader(param);
		env.setHeader(header);
		//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
		//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        
        SOAPBody body = new SOAPBody();
        SOAPMethod method = new SOAPMethod(new SOAPQName(SOAPConsts.MM7DeliverResMethodName, strdeliveryResponsePrefix, namespace), (Vector) null);
        
        method.addParameter(SOAPConsts.MM7MM7VersionParameterName, mm7Version);
        
        SOAPParameter status = method.addParameter(SOAPConsts.MM7StatusParameterName);
        
        if (statusText != null && statusText.length() > 0) {
            status.addParameter(SOAPConsts.MM7StatusTextParameterName, statusText);
        }
        
        status.addParameter(SOAPConsts.MM7StatusCodeParameterName, String.valueOf(statusCode.getCode()));
        
        // add service code only if the namespace is not REL-7-MM7-1-0
        if (!namespace.equalsIgnoreCase(Namespace.REL_5_MM7_1_0.toString())) {
            if (serviceCode != null && serviceCode.length() > 0) {
                method.addParameter(SOAPConsts.MM7ServiceCodeParameterName, serviceCode);
            }
        }
        
        body.setMethod( method );
        env.setBody( body );
        return env;
    }
    
    private String serviceCode;

}
