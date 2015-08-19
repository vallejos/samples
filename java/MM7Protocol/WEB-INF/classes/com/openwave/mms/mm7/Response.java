package com.openwave.mms.mm7;

import java.io.IOException;
import java.io.OutputStream;

import javax.servlet.ServletResponse;

import org.apache.log4j.Logger;

import com.openwave.mms.mm7.soap.SOAPConsts;
import com.openwave.mms.mm7.soap.SOAPEnvelope;
import com.openwave.mms.mm7.soap.SOAPException;

/**
 *   This abstract base class encapsulates attributes common to all 
 *   incoming and outgoing responses. 
 */
public abstract class Response {

    /**
     *  Instantiates a <code>Response</code> object.
     */
    protected Response() {
        mm7Version = SOAPConsts.MM7MM7VersionParameterValue[0];
        statusCode = ErrorCode.SUCCESS;
        namespace = SOAPConsts.MM7Namespaces[3];
    }

    /**
     *  Gets the transaction ID of this incoming <code>Response</code> 
     *  object that uniquely identifies it.
     *
     *  @return The transaction ID of this response.
     */
    public String getTransactionID() { return transactionID; }

    /**
     *  Sets the transaction ID of this outgoing <code>Response</code> 
     *  object to uniquely identify it.
     *  This method is typically for API internal use only.
     *
     *  @param tid The transaction ID of this response.
     */
    void setTransactionID( String tid ) { transactionID = tid; }

    /**
     *  Sets the namespace to be used for the response.
     *
     *  @param namespace The namespace to be used.
     */
    void setNamespace( String namespace ) { this.namespace = namespace; }

    /**
     *  Gets the version of the MM7 schema that MMSC used to generate this
     *  incoming response.
     *
     *  @return The MM7 version used by MMSC to generate this response.
     */
    public String getMM7Version() { return mm7Version; }

    /**
     *  Gets the text that describes the status of the outgoing request to which
     *  this is the incoming response.
     *
     *  @return The status description of the request to which this is the response.
     */
    public String getStatusText() { return statusText; }

     /**
     *  Gets the status code of the outgoing request to which
     *  this is the incoming response.
     *
     *  @return The status code of the request to which this is the response, 
     *     as an {@link ErrorCode} object.
     */
    public ErrorCode getStatusCode() { return statusCode; }

    /**
     *  Method that the {@link RelayConnection} class uses to write this response
     *  to an <code>OutputStream</code> object. This method is for API internal use only???
     *
     *  @param outputStream The <code>OutputStream</code> object to which this object is written.
     *  @throws IOException If the API encounters an error while writing the response to 
     *                      the output stream.
     *  @throws SOAPException If any of the SOAP Envelope creation rules are violated.
     *
     */
    void writeTo( OutputStream outStream )
                  throws IOException, SOAPException {
        SOAPEnvelope env = serialize();
        String envStr = env.toString();
        String header = "Content-length: " + envStr.length() + "\r\n\r\n";
        outStream.write( header.getBytes() );
        outStream.write( envStr.getBytes() );
        outStream.flush();

        if( logger.isDebugEnabled() ) {
            logger.debug( "Content-length: " + envStr.length() + "\r\n\r\n" );
            logger.debug( envStr );
        }
    }

    void writeTo( ServletResponse response )
                  throws IOException, SOAPException {
        SOAPEnvelope env = serialize();
        String envStr = env.toString();
        response.setContentLength( envStr.length() );;
        response.getWriter().print( envStr );
        response.getWriter().flush();

        if( logger.isDebugEnabled() ) {
            logger.debug( "Content-length: " + envStr.length() + "\r\n\r\n" );
            logger.debug( envStr );
        }
    }

    /**
     *  Protected method that is implemented by all subclasses of the 
     *  <code>Response</code> class and used to write this <code>Response</code>  
     *  object to an output stream. This method 
     *  is for API internal use only.
     *
     *  @throws SOAPException If any SOAP Envelope formation rules are  
     *         violated while writing the object.
     *
     */
    protected abstract SOAPEnvelope serialize() throws SOAPException;

    /** Protected value that specifies the transaction ID, which uniquely 
     *  identifies this response.
     *  To retrieve the transaction ID, use the {@link #getTransactionID 
     *  TransactionID} method.  
     */
    protected String transactionID;

    /** Protected value that specifies the version of the MM7 schema used
     *  to generate this response.
     *  To retrieve the MM7 version for this response, use the 
     *  {@link #getMM7Version getMM7Version} method.  
     */
    protected String mm7Version;

    /** Protected text description that describes the status of the request 
     *  for which this is the <code>Response</code> object.
     *  To retrieve the status text for this response, use the 
     *  {@link #getStatusText getStatusText} method.  
     */
    protected String statusText;

    /** Protected {@link ErrorCode} object that specifies the status code
     *  of the request for which this is the <code>Response</code> object.
     *  To retrieve the status code for this response, use the 
     *  {@link #getStatusCode getStatusCode} method.  
     */
    protected ErrorCode statusCode;

    /**
     *  The Namespace to be used when serializing this response.
     */
    protected String namespace;

    private static final Logger logger = Logger.getLogger( Response.class );

}
