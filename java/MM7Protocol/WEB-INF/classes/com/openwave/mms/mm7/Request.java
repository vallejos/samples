package com.openwave.mms.mm7;

import java.io.IOException;
import java.io.OutputStream;

import com.openwave.mms.mm7.soap.SOAPBody;
import com.openwave.mms.mm7.soap.SOAPConsts;
import com.openwave.mms.mm7.soap.SOAPEnvelope;
import com.openwave.mms.mm7.soap.SOAPException;
import com.openwave.mms.mm7.soap.SOAPHeader;
import com.openwave.mms.mm7.soap.SOAPMethod;
import com.openwave.mms.mm7.soap.SOAPParameter;
import com.openwave.mms.mm7.util.Utils;

import com.openwave.mms.content.ContentException;

/**
 *   This abstract base class encapsulates attributes common to all outgoing message requests. 
 *   When creating a request message object, you can identify the message originator using the 
 *   {@link #setSenderAddress setSenderAddress}, {@link #setVasId setVasId}, or 
 *   {@link #setVaspID setVaspID} method.
 */
public abstract class Request {

    /**
     *  Instantiates a Request object.
     */
    protected Request() {
        mm7Version = SOAPConsts.MM7MM7VersionParameterValue[0];
    }

    /**
     *  This abstract method is expected to be implemented by all subclasses to
     *  write themselves to an <code>OutputStream</code>. This method
     *  is used for internal API use only.
     *
     *  @param outputStream The OutputStream object to which the API writes the data.
     *  @throws APIException If the API encounters an error while composing the message from mime 
     *          body parts or writing the message to the output stream.
     *  @throws SOAPException If any of the SOAP Envelope creation rules are violated.
     *  @throws ContentException If the content processing results in an error.
     *                        
     */
    protected abstract void writeTo(OutputStream outputStream) throws APIException, ContentException, SOAPException;

    /**
     *  Sets the transaction ID of an outgoing message that uniquely specifies the request.
     *  If the client application does not use this method to set an ID, the API 
     *  assigns the message a globally unique identifier (GUID).
     *
     *  @param transactionId The transaction ID of this request.
     */
    public void setTransactionID(String transactionId) {
        this.transactionId = transactionId;
    }

    /**
     *  Sets the ID that identifies the value-added service provider (VASP) originating 
     *  this message request.
     *
     *  @param vaspId The ID of the VASP that originates this message request.
     */
    public void setVaspID(String vaspId) {
    	this.vaspId = vaspId;
    }

    /**
     *  Sets the ID that identifies the value-added service (VAS) application originating  
     *  this message request.
     *
     *  @param vasId The ID of the VAS that originates this message request.
     */
    public void setVasId(String vasId) {
    	this.vasId = vasId;
    }

    /**
     *  Sets the address of the sender originating this message request.
     *  This method assumes that the address is a PLMN.
     *
     *  @param senderAddress The address of the sender that originates this message request.
     *  @deprecated Use the other form of setSenderAddress
     */
    public void setSenderAddress(String senderAddress) {
        this.senderAddress = senderAddress;
        this.senderAddressType = AddressType.NUMBER;
    }

    /**
     *  Sets the address of the sender originating this message request.
     *
     *  @param senderAddress The address of the sender that originates this message request.
     *  @param senderAddressType The type of address in the above parameter.
     */
    public void setSenderAddress(String senderAddress, AddressType senderAddressType) {
        this.senderAddress = senderAddress;
        this.senderAddressType = senderAddressType;
    }

    /**
     *  Method used by writeTo to serialize the contents of the parent request object into
     *  a SOAP Envelope. This method will not be used by client applications.
     *
     *  @param env The envelope to serialize to.
     *
     */
    void serialize(SOAPEnvelope env) throws APIException {
        try {
            if ((vaspId == null || vaspId.length() == 0) && (vasId == null || vasId.length() == 0) && (senderAddress == null || senderAddress.length() == 0)) {
                throw new APIException("one of VASPID, VASID or SenderAddress must be set");
            }

            if (transactionId == null || transactionId.length() == 0) {
                transactionId = Utils.generateTransactionID();
            }
    
            SOAPMethod method = env.getBody().getMethod();
            SOAPHeader header = env.getHeader();
            header.addHeader(mm7Namespace, SOAPConsts.MM7TransactionIDParameterName, transactionId);
            method.addParameter(SOAPConsts.MM7MM7VersionParameterName, SOAPConsts.MM7MM7VersionParameterValue[0]);
            SOAPParameter senderId = method.addParameter(SOAPConsts.MM7SenderIdentificationParameterName);

            if (vaspId != null && vaspId.length() > 0)
                senderId.addParameter(SOAPConsts.MM7VASPIDParameterName, vaspId);
            
            if (vasId != null && vasId.length() > 0)
                senderId.addParameter(SOAPConsts.MM7VASIDParameterName, vasId);
            
            if (senderAddress != null && senderAddress.length() > 0) {
                if (mm7Namespace.equals( Namespace.REL_5_MM7_1_0.toString()) || mm7Namespace.equals(Namespace.REL_5_MM7_1_1.toString())) {
                    senderId.addParameter(SOAPConsts.MM7SenderAddressParameterName).setValue(senderAddress);
                }else{
                    SOAPParameter senderAddressParam = senderId.addParameter(SOAPConsts.MM7SenderAddressParameterName);
                    senderAddressParam.addParameter(senderAddressType.toString()).setValue(senderAddress);
                }
            }
            
        }catch(SOAPException se) {
            throw new APIException("soap-exception", se.getMessage());
        }
    }

    /**
     * This method can be used to set the namespace to be used for all
     * requests from VASP to the MMSC. The default is REL-5-MM7-1-3.
     *
     * @param namespace The namespace to be set.
     */
    public static final void setNamespace(Namespace namespace) {
        if (namespace != null) {
            mm7Namespace = namespace.toString();
        } // else use the default already set
    }

    protected static String mm7Namespace = Namespace.REL_5_MM7_1_3.toString();
    private String mm7Version;
    private String transactionId;
    private String vaspId;
    private String vasId;
    private String senderAddress;
    private AddressType senderAddressType;

}
