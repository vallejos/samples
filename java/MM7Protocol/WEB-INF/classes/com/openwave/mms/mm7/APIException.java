package com.openwave.mms.mm7;

import java.util.Locale;
import java.util.MissingResourceException;
import java.util.ResourceBundle;

/**
 *  This class encapsulates the exception that is thrown when the API encounters an error.
 *  Use the accessor methods in this class to determine the exact error that occurs. 
 *  When an <code>APIException</code> is thrown, the following information is available:
 *  <UL>
 *  <LI> Localized error message
 *  <LI> Nonlocalized error message 
 *  <LI> Error code
 *  <LI> Flag indicating whether it is possible to retry the operation
 *  </UL>
 *  <p>
 *  For information about using <code>APIException</code> objects to handle API errors,
 *  or about localized and nonlocalized error messages that are stored in Java 
 *  <code>MessageBundles</code>, see the <em>Openwave MMS Library Developer's Guide</em>.
 *  </p>
 */
public class APIException extends Exception {

   /**
    *  Instantiates an <code>APIException</code> object with the error reason.
    *
    *  @param reason The reason that the error ocurred. Must be one of the predefined reasons 
    *          in the Java <code>MessageBundles</code> that corresponds to an error message.  
    */  
    APIException( String reason ) {
        errorCode = ErrorCode.CLIENT_ERROR;
        this.reason = reason;
    }

   /**
    *  Instantiates an <code>APIException</code> object with the error code and reason.
    *
    *  @param errorCode The code associated with the error. Must be an {@link ErrorCode} object.  
    *  @param reason The reason that the error ocurred. Must be one of the predefined reasons 
    *          in the Java <code>MessageBundles</code> that corresponds to an error message.  
    */  
    APIException( ErrorCode errorCode, String reason ) {
        this.errorCode = errorCode;
        this.reason = reason;
    }

   /**
    *  Instantiates an <code>APIException</code> object with the error reason and additional 
    *  application-specific error-related information.
    *
    *  @param reason The reason that the error ocurred. Must be one of the predefined reasons 
    *          in the Java <code>MessageBundles</code> that corresponds to an error message.  
    *  @param additionalParam Additional error information for use by the application.  
    */  
    APIException( String reason, Object additionalParam ) {
        errorCode = ErrorCode.CLIENT_ERROR;
        this.reason = reason;
        this.additionalParam = additionalParam;
    }

   /**
    *  Instantiates an <code>APIException</code> object with the error code, reason, and additional 
    *  application-specific error-related information.
    *
    *  @param errorCode The code associated with the error. Must be an {@link ErrorCode} object.  
    *  @param reason The reason that the error ocurred. Must be one of the predefined reasons 
    *          in the Java <code>MessageBundles</code> that corresponds to an error message.  
    *  @param additionalParam Additional error information for use by the application.  
    */  
    APIException( ErrorCode errorCode, String reason, Object additionalParam ) {
        this.errorCode = errorCode;
        this.reason = reason;
        this.additionalParam = additionalParam;
    }

    /**
     *  Gets a localized version of the error message that is associated with this exception. 
     *  This method returns the error text from the 
     *  <code>ErrorMessages_<em>lang</em>_<em>country</em>.properties</code> Java 
     *  <code>MessageBundle</code>. 
     *  Localized error messages are typically used for logging the message locally.     
     *  <p>By default, the text in this file is in U.S. English. For information on
     *  localizing the text in this file, see the <em>Openwave MMS Library Developer's 
     *  Guide</em>. </p>
     *
     *  @return The localized error message text.
     */
    public String getLocalizedMessage() {
        try {
            String ret = ResourceBundle.getBundle( "resources.ErrorMessages" ).getString( reason );
            if( ret == null || ret.length() == 0 )
                ret = reason;
            if( additionalParam == null )
                return ret;
            else
                return ret + ": " + additionalParam;
        } catch( MissingResourceException mre ) {
            return "cannot get localized message from resource bundle: " + mre.getMessage();
        }
    }

    /**
     *  Gets a nonlocalized version of the error message that is associated with this exception.
     *  This method returns the error text from the <code>ErrorMessages_en.properties</code>
     *  Java <code>MessageBundle</code>. 
     *  Nonlocalized error messages are typically used in the SOAP faultstring element to return
     *  error responses using the {@link FaultResponse} class.
     *
     *  @return The nonlocalized error message.
     */
    public String getMessage() {
        String ret = null;
        try {
            ret = ResourceBundle.getBundle( "resources.ErrorMessages",
                                            Locale.US ).getString( reason );
        } catch( MissingResourceException mre ) {
            ret = reason;
        }
        if( ret == null || ret.length() == 0 )
            ret = reason;
        if( additionalParam == null )
            return ret;
        else
            return ret + ": " + additionalParam;
    }

    /**
     *  Gets the error code that is associated with the error that caused this exception.
     *
     *  @return The error code, as an {@link ErrorCode} object.
     */
    public ErrorCode getErrorCode() { return errorCode; }

    /**
     *  Specifies whether you can retry the operation that caused this exception.
     *  You can typically retry errors that are not severe.
     *
     *  @return A boolean value that specifies whether you can retry the operation. 
     *         <code>true</code> indicates that you can retry the operation, <code>false</code> 
     *         indicates that you cannot.
     */
    public boolean isRetryable() { return retryable; }

    /**
     *  Sets whether you can retry the operation that caused this exception.
     *  You can typically retry errors that are not severe.
     *
     *  @param retryable A Boolean value that specifies whether the operation can be retried. 
     *         <code>true</code> indicates that the operation can be retried, <code>false</code> 
     *         indicates that it cannot.
     */
    void setRetryable( boolean retryable ) { this.retryable = retryable; }

    private String reason;
    private ErrorCode errorCode;
    private Object additionalParam;
    private boolean retryable;

}
