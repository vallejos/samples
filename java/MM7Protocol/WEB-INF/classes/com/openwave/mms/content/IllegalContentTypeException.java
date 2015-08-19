// EDITOR NOTE: Please set number of columns to 100 in ur editor.

package com.openwave.mms.content;

/**
 * This exception is thrown when the content-type header in a MIME formatted message
 * cannot be parsed.
 */

public class IllegalContentTypeException extends ContentException {

    /**
     * This constructor takes a reason string as input.
     *
     * @param reason The reason for throwing this exception.
     */
    IllegalContentTypeException( String reason ) {
        super( reason );
    }

    /**
     * This constructor takes a reason string as input.
     *
     * @param reason The reason for throwing this exception.
     * @param exception The exception wrapped in this exception.
     */
    IllegalContentTypeException( String reason, Throwable exception ) {
        super( reason, exception );
    }

}
