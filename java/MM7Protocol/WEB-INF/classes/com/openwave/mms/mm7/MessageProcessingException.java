package com.openwave.mms.mm7;

/**
 *  A <code>MessageProcessingException</code> is typically thrown in the methods of a
 *  custom object that implements the {@link MessageListener} interface or extends the
 *  {@link MessageListenerAdapter} class. Throwing a <code>MessageProcessingException</code>
 *  causes the API to automatically generate a {@link FaultResponse} object that contains the error. 
 *  <p>
 *  For information about using <code>MessageProcessingException</code> objects to 
 *  handle errors that occur when processing <code>Request</code> objects,
 *  see the <em>Openwave MMS Library Developer's Guide</em>.
 *  </p>
 */
public class MessageProcessingException extends Exception {

    /**
     *  Instantiates a <code>MessageProcessingException</code> object.
     *
     *  @param reason A <code>String</code> object that describes the error.
     *  @param errorCode The error code, as an {@link ErrorCode} object.
     */
    public MessageProcessingException( String reason,
                                       ErrorCode errorCode ) {
        this.errorCode = errorCode;
        this.reason = reason;
    }

    /**
     *  Gets the description of the error condition that caused this exception.
     *
     *  @return A <code>String</code> object that describes the error.
     */
    public String getMessage() { return reason; }

    /**
     *  Gets the error code that is associated with the error that caused this exception.
     *
     *  @return The error code, as an {@link ErrorCode} object.
     */
    public ErrorCode getErrorCode() { return errorCode; }

    private String reason;
    private ErrorCode errorCode;

}
