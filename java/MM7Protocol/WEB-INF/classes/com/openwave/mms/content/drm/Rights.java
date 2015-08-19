package com.openwave.mms.content.drm;

import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;

import javax.mail.BodyPart;
import javax.mail.MessagingException;

/**
 * This class represents the DRM rights associated with an object.
 * As of this release only forward locking is fully implemented in
 * this API. This class stores/returns a string representation of the
 * rights. A future relase of the API will support the OMA DRM Rights
 * object completely.
 */

public class Rights {
    /**
     * Creates a rights object from a string representation of the rights.
     *
     * @param rights Rights expressed as a string.
     */
    public Rights( String rights ) {
        this.rights = rights;
    }

    /**
     * Returns the rights object as a string.
     *
     * @return The rights object in string form.
     */
    public String toString() {
        return rights;
    }

    private String rights;
}
