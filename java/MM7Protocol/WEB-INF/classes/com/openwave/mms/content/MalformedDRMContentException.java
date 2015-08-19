package com.openwave.mms.content;

import com.openwave.mms.content.ContentException;

/**
 * This exception is thrown when the DRM content is malformed.
 */

public class MalformedDRMContentException extends ContentException {

    /**
     * This constructor takes a reason string as input.
     *
     * @param reason The reason for throwing this exception.
     */
    MalformedDRMContentException( String reason ) {
        super( reason );
    }

    /**
     * This constructor takes a reason string as input.
     *
     * @param reason The reason for throwing this exception.
     * @param exception The exception wrapped in this exception.
     */
    MalformedDRMContentException( String reason, Throwable exception ) {
        super( reason, exception );
    }

}
