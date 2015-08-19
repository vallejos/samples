// EDITOR NOTE: Please set number of columns to 100 in ur editor.

package com.openwave.mms.content;

/**
 * This exception is thrown when there is no SMIL part found while parsing a MIME 
 * formatted input stream.
 */

public class SmilNotFoundException extends ContentException {

    /**
     * This constructor takes a reason string.
     *
     * @param reason The reason for throwing this exception.
     */
    SmilNotFoundException( String reason ) {
        super( reason );
    }

}
