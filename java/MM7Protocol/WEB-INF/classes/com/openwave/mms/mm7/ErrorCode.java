package com.openwave.mms.mm7;

import java.util.HashMap;

/**
 *  This class encapsulates the standard MM7 status and error codes that describe
 *  the type of success or failure that occurs while processing a request message.
 *  <p>
 *  Status and error values are used in: 
 *  <UL><LI>Response messages to return the status or error of the request. 
 *  Use this class when setting or retreiving the status or error value from response 
 *  objects.
 *
 *  <LI> Log files to document the success or failure of request messages. 
 *  <code>ErrorCode</code> class methods such as {@link #getErrorCodeByName 
 *  getErrorCodeByName} provide ways of converting between error codes and names 
 *  so that you can produce more readable log entries. For example, you can convert 
 *  the error code <code>2001</code> to <code>OperationRestricted</code>.
 *  </UL>
 * 
 *  Status and error codes are grouped into four classes:
 *  <UL>
 *  <LI><b>Success (1xxx)</b> The action was successfully received, understood, and accepted.
 *  <LI><b>Client Error (2xxx)</b> The request contains bad syntax or cannot be fulfilled.
 *  <LI><b>Server Error (3xxx)</b> The server failed to fulfill an apparently valid request.
 *  <LI><b>Service Error (4xxx)</b> The service could not be performed. The operation may be 
 *      retried.
 *  </UL>
 *  Codes in the range x500 to x999 are specific to the API and are not MM7 standard errors.
 *  <p>
 *  For further information about the API uses error and status codes, 
 *  see the <em>Openwave MMS Library Developer's Guide</em>.
 */
public final class ErrorCode {

    private static final HashMap NAME_KEYED_ERROR_MAP = new HashMap();
    private static final HashMap CODE_KEYED_ERROR_MAP = new HashMap();

    private ErrorCode( String name, int code ) {
        this.name = name;
        this.code = code;
        NAME_KEYED_ERROR_MAP.put( name.toLowerCase(), this );
        CODE_KEYED_ERROR_MAP.put( new Integer( code ), this );
    }

    /**
     *  Converts the information that this error object contains to the format 
     *  <code><em>ErrorName:ErrorCode</em></code>. This method overrides the 
     *  <code>toString</code> method of the <code>Object</code> class to 
     *  provide a readable text string. 
     */
    public String toString() { return name + ':' + code; }

    /** 
     * Gets the name associated with this error or status value.
     *
     * @return The name of the error or status value.
     */
    public String getName() { return name; }

    /** Gets the integer code associated with this error or status value.
     *
     * @return The code for the error or status value.
     */
    public int getCode() { return code; }

    /**
     *  Gets an <code>ErrorCode</code> object that corresponds to 
     *  the status or error name specified.
     *
     *  @param name The name of the status or error.
     *  @return The <code>ErrorCode</code> object that corresponds to <code>name</code>.
     */
    public static ErrorCode getErrorCodeByName( String name ) {
        return ( ErrorCode ) NAME_KEYED_ERROR_MAP.get( name.toLowerCase() );
    }

    /**
     *  Gets an <code>ErrorCode</code> object that corresponds to the integer
     *  status or error code specified.
     *
     *  @param code The code for the status or error.
     *  @return The <code>ErrorCode</code> object that corresponds to <code>code</code>.
     */
    public static ErrorCode getErrorCodeByCode( int code ) {
        return ( ErrorCode ) CODE_KEYED_ERROR_MAP.get( new Integer( code ) );
    }

    /** 
     * Static constant that specifies the error code (1000) for success. 
     * <p>
     * This status occurs when the request was accepted successfullly.
     */
    public static final ErrorCode SUCCESS = new ErrorCode( "Success", 1000 );

    /** 
     * Static constant that specifies the error code (1100) for partial success. 
     * <p>
     * This status occurs when some parts of the request did not execute completely.
     * Additional details may be provided by the final three digits of the status
     * code and the <code>Details</code> data element. 
     */
    public static final ErrorCode PARTIAL_SUCCESS = new ErrorCode( "PartialSuccess", 1100 );

    /** 
     * Static constant that specifies the error code (2000) for a client error. 
     * <p>
     * This error occurs when the client performs an invalid request. 
     */
    public static final ErrorCode CLIENT_ERROR = new ErrorCode( "ClientError", 2000 );

    /** 
     * Static constant that specifies the error code (2001) for a restricted operation.
     * <p>
     * This error occurs when the client does not have the correct permissions to  
     * execute the request.
     */
    public static final ErrorCode OPERATION_RESTRICTED = new ErrorCode( "OperationRestricted", 2001 );

    /** 
     * Static constant that specifies the error code (2002) for an address error.
     * <p>
     * This error occurs when the client supplies an address that is not valid because 
     * the  address either:
     * <UL>
     * <LI>Contains an invalid format
     * <LI>Is not serviced by MMSC (if returned by MMSC)
     * </UL>
     * If MMSC returns this status in a submit response message that was addressed to mulitple
     * recipients, it indicates that at least one address was incorrect.
     */
    public static final ErrorCode ADDRESS_ERROR = new ErrorCode( "AddressError", 2002 );

    /** 
     * Static constant that specifies the error code (2003) when the address is not found. 
     * <p>
     * This error occurs if the address of a recipient is not found.
     */
    public static final ErrorCode ADDRESS_NOT_FOUND = new ErrorCode( "AddressNotFound", 2003 );

    /** 
     * Static constant that specifies the error code (2004) for unacceptable content. 
     */
    public static final ErrorCode UNACCEPTABLE_CONTENT = new ErrorCode( "UnacceptableContent", 2004 );

    /** 
     * Static constant that specifies the error code (2005) when the message ID is not found. 
     * <p>
     * This occurs when the client requests an operation on a previously submitted message 
     * and the server cannot locate the message with that ID.
     */
    public static final ErrorCode MESSAGE_ID_NOT_FOUND = new ErrorCode( "MessageIDNotFound", 2005 );

    /** 
     * Static constant that specifies the error code (2006) when the linked ID is not found.
     * <p>
     * This error occurs when the client sends a messatge that contains a <code>LinkedID</code>
     * for which the server cannot find the related message with the same <code>LinkedID</code>.
     */
    public static final ErrorCode LINKED_ID_NOT_FOUND = new ErrorCode( "LinkedIDNotFound", 2006 );

    /** 
     * Static constant that specifies the error code (2007) when the format for the value of 
     * a data element is incorrect. 
     * <p>
     * This error occurs when the client sends a message that contains an element with an incorrect
     * or inappropriate value format.
     */
    public static final ErrorCode INCORRECT_ELEMENT_VALUE_FORMAT = new ErrorCode( "IncorrectElementValueFormat", 2007 );

    /** 
     * Static constant that specifies the error code (3000) for a server error.
     * <p>
     * This error occurs when the server fails to complete an apparently valid request.
     */
    public static final ErrorCode SERVER_ERROR = new ErrorCode( "ServerError", 3000 );

    /** 
     * Static constant that specifies the error code (3001) when the operation is not possible.
     * <p>
     * This error occurs when the server fails to complete the request because the requested
     * operation is not possible.
     */
    public static final ErrorCode NOT_POSSIBLE = new ErrorCode( "NotPossible", 3001 );

    /** 
     * Static constant that specifies the error code (3002) when the message is rejected.
     * <p>
     * This error occurs when the server could not complete the service requested.
     * <UL>
     * <LI>The server is unable to parse correctly
     * <LI>Exceeds the maximum content size specified in the service-level agreement
     * <LI>Contains an unacceptable content type
     * </UL>
     */   
    public static final ErrorCode MESSAGE_REJECTED = new ErrorCode( "MessageRejected", 3002 );

    /** 
     * Static constant that specifies the error code (3003) when multiple addresses are 
     * supplied but are not supported by the MMSC. 
     * <p>
     * This error occurs when the server (MMSC) does not support the requested operation 
     * on multiple recipients. The client (application) may choose to resubmit the request 
     * as several single-recipient requests. 
     */   
    public static final ErrorCode MULTIPLE_ADDRESSES_NOT_SUPPORTED = new ErrorCode( "MultipleAddressesNotSupported", 3003 );

    /** 
     * Static constant that specifies the error code (4000) for a general service error.
     * <p>
     * This error occurs when the server cannot fulfill the requested service.
     */
    public static final ErrorCode GENERAL_SERVICE_ERROR = new ErrorCode( "GeneralServiceError", 4000 );

    /** 
     * Static constant that specifies the error code (4001) when an improper ID is supplied.
     * <p>
     * This error occurs when the server or client cannot uniquely identify the client from
     * the identification header of the request.
     */
    public static final ErrorCode IMPROPER_ID = new ErrorCode( "ImproperID", 4001 );

    /** 
     * Static constant that specifies the error code (4002) when the MM7 version is not supported.
     * <p>
     * This error occurs when the server does not support the MM7 version specified
     * in the request (<code>MM7Version</code> field).
     */
    public static final ErrorCode UNSUPPORTED_VERSION = new ErrorCode( "UnsupportedVersion", 4002 );

    /** Static constant that specifies the error code (4003) when the operation is not supported.
     * <p>
     * This error occurs when the server does not support the request type specified by the 
     * <code>MessageType</code> header field of the message.
     */ 
    public static final ErrorCode UNSUPPORTED_OPERATION = new ErrorCode( "UnsupportedOperation", 4003 );

    /** Static constant that specifies the error code (4004) for a validation error.
     * <p>
     * This error occurs when the server or client generates a parsing error because the message either:
     * <UL>
     * <LI>Contains improperly formatted SOAP and XML structures
     * <LI>Does not contain all mandatory data elements
     * <LI>Contains a format that is not compatible with the format for the specified request
     * </UL>
     * Additional details about the parsing error may be provided by the <code>Details</code>
     * data element.
     */ 
    public static final ErrorCode VALIDATION_ERROR = new ErrorCode( "ValidationError", 4004 );

    /** 
     * Static constant that specifies the error code (4005) for a service error.
     * <p>
     * This error occurs when the server generates an error while performing the requested
     * operation. 
     */
    public static final ErrorCode SERVICE_ERROR = new ErrorCode( "ServiceError", 4005 );

    /** 
     * Static constant that specifies the error code (4006) when the service is not available.
     * <p>
     * This error occurs when the server is temporarily unavailable, for example if the server
     * is under heavy load and does not have the resources to handle the request. 
     */
    public static final ErrorCode SERVICE_UNAVAILABLE = new ErrorCode( "ServiceUnavailable", 4006 );

    /** Static constant that specifies the error code (4007) when the service is denied.
     * <p>
     * This error occurs when the client does not have the correct permissions or funds to 
     * perform the requested operation. 
     */
    public static final ErrorCode SERVICE_DENIED = new ErrorCode( "ServiceDenied", 4007 );

//our own extensions

    /** 
     * Static constant that specifies the error code (2500) when the transaction ID is missing.
     * <p>
     * This error occurs when the client sends a message that does not contain the
     * <code>TransactionID</code> data element.
     */
    public static final ErrorCode TRANSACTION_ID_MISSING = new ErrorCode( "TransactionIDMissing", 2500 );

    /** Static constant that specifies the error code (2501) when the format for the value of 
     *  an attribute is incorrect.
     * <p>
     * This error occurs when the client sends a message that contains an element having an
     * associated attribute with either an incorrect or an inappropriate format.
     */
    public static final ErrorCode INCORRECT_ATTRIBUTE_VALUE_FORMAT = new ErrorCode( "IncorrectAttributeValueFormat", 2501 );

    /** Static constant that specifies the error code (2502) when a required attribute is missing.
     * <p>
     * This error occurs when the client sends a message that contains an element that is
     * missing a required associated attribute.
     */
    public static final ErrorCode REQUIRED_ATTRIBUTE_NOT_FOUND = new ErrorCode( "RequiredAttributeNotFound", 2502 );

    /** Static constant that specifies the error code (2503) when a required element is missing.
     * <p>
     * This error occurs when the client sends a message that is missing a required element.
     */
    public static final ErrorCode REQUIRED_ELEMENT_NOT_FOUND = new ErrorCode( "RequiredElementNotFound", 2503 );

    /** Static constant that specifies the error code (2504) when a user's device profile is not found.
     * <p>
     * This error occurs when the MMSC fails to find a user's device profile when requested
     */
    public static final ErrorCode USER_DEVICE_NOT_FOUND = new ErrorCode( "UserDeviceProfileNotFound", 2504 );

    /** Static constant that specifies the error code (2999) for a threading error.
     * <p>
     * This error occurs when the client code attempts to call methods on a {@link RelayConnection}
     * object in a different thread than the one in which it was created. The same thread that 
     * creates a <code>RelayConnection</code> object must be used to call its methods.
     */
    public static final ErrorCode THREADING_ERROR = new ErrorCode( "ThreadingError", 2999 );

    private String name;
    private int code;

}
