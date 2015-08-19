package com.globalnet.mm7;

import java.util.Locale;
import java.util.MissingResourceException;
import java.util.ResourceBundle;

public class MM7SubmitRequestException extends Exception {

   /**
    *  Instantiates an <code>APIException</code> object with the error reason.
    *
    *  @param reason The reason that the error ocurred. Must be one of the predefined reasons 
    *          in the Java <code>MessageBundles</code> that corresponds to an error message.  
    */  
    MM7SubmitRequestException( String reason ) {
        this.reason = reason;
    }

    private String reason;

}
