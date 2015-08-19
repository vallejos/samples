// EDITOR NOTE: Please set number of columns to 100 in ur editor.

package com.openwave.mms.content;

import java.util.Locale;
import java.util.MissingResourceException;
import java.util.ResourceBundle;

/**
 * This class encapsulates the exception that is thrown when the API encounters an error
 * processing multimedia content. Use the accessor methods in this class to determine the
 * exact error that occurs. When a <code>ContentException</code> is thrown, the following
 * information is available:
 * <ul>
 * <li> Localized error message
 * <li> Nonlocalized error message 
 * </ul>
 * <p>
 * Error messages provided by this API can be localized. The API is shipped with a 
 * properties file called ErrorMessages.properties. Localized versions of this file
 * can be created and placed anywhere in the classpath. This class provides a static
 * method, <code>setResourceBundle</code>, which can be used to set the approriate
 * resource bundle to be used.
 * </p>
 * <p>
 * For information about using <code>ContentException</code> objects to handle content
 * processing errors, or about localized and nonlocalized error messages that are stored
 * in Java <code>ResourceBundle</code>s, see the <em>Openwave MMS Library Developer's Guide</em>.
 * </p>
 */
public class ContentException extends Exception {

   /**
    * Instantiates a ContentException object with the error reason.
    *
    * @param reason The reason that the error occurred. It must be one of the predefined reasons 
    *        in the ErrorMessages class.  
    */  
    ContentException( String reason ) {
        this.reason = reason;
    }

   /**
    * Instantiates a ContentException object with the error reason. The
    * second argument can be used to pass in extra information.
    *
    * @param reason The reason that the error ocurred. It must be one of the predefined reasons 
    *        in the ErrorMessages class.  
    */  
    ContentException( String reason, String additionalParam ) {
        this.reason = reason;
        this.additionalParam = additionalParam;
    }

   /**
    * Instantiates a ContentException object with the error reason and wraps 
    * another exception.
    *
    * @param reason The reason that the error ocurred. It must be one of the predefined reasons 
    *        in the ErrorMessages class.  
    * @param exception An exception that this object wraps.
    */  
    ContentException( String reason, Throwable exception ) {
        this.reason = reason;
        this.exception = exception;
    }

    /**
     * Gets a localized version of the error message that is associated with this exception 
     * by reading from the <code>ResourceBundle</code> specified using <code>
     * setResourceBundle</code>. If no resource bundle was specified, it returns an English
     * message.
     *
     * @return The localized error message text.
     */
    public String getLocalizedMessage() {
        String ret = null;
        if( resourceBundle != null ) {
            try {
                ret = resourceBundle.getString( reason );
                if( ret == null || ret.length() == 0 )
                    ret = reason;
            } catch( MissingResourceException mre ) {
                return "cannot get localized message for [" + reason +
                       "] from resource bundle: " + mre.getMessage();
            }
        } else {
            ret = reason;
        }

        return additionalParam == null ? ret  : ret + additionalParam;
    }

    /**
     * Gets a nonlocalized version of the error message that is associated with this exception.
     *
     * @return The nonlocalized error message.
     */
    public String getMessage() {
        return additionalParam == null ? reason  : reason + additionalParam;
    }

    /**
     * Returns the exception that is wrapped within this object.
     *
     * @return The wrapped exception.
     */
    public Throwable getWrappedException() {
        return exception;
    }

    /**
     * Specifies an application-specific <code>ResourceBundle</code>.
     *
     * @param bundle The <code>ResourceBundle</code> to be used for getting localized
     *        error messages.
     */
    public static void setResourceBundle( ResourceBundle bundle ) {
        resourceBundle = bundle;
    }

    private static ResourceBundle resourceBundle = new ErrorMessages();
    private String reason;
    private Throwable exception;
    private String additionalParam;

}
