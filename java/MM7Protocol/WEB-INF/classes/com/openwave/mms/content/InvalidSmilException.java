// EDITOR NOTE: Please set number of columns to 100 in ur editor.

package com.openwave.mms.content;

/**
 * This exception is thrown when SMIL synthesis or parsing fails.
 */

public class InvalidSmilException extends ContentException {

    /**
     * This constructor takes a reason string and wraps another exception.
     *
     * @param reason The reason for throwing this exception.
     * @param exception The exception wrapped within this exception.
     */
    InvalidSmilException( String reason, Throwable exception ) {
        super( reason, exception );
    }

}
