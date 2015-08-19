// EDITOR NOTE: Please set number of columns to 100 in ur editor.

package com.openwave.mms.content;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;

import javax.mail.BodyPart;
import javax.mail.MessagingException;
import javax.mail.internet.MimeBodyPart;
import javax.mail.internet.MimeUtility;

import javax.activation.DataHandler;
import javax.activation.FileDataSource;

/**
 * This class represents a media object of type image.
 */

public final class Image extends MediaObject {

    /**
     * The default constructor.
     */
    public Image() {
    }

    /**
     * Constructs an Image object from an existing JavaMail BodyPart.
     *
     * @param bodyPart The body part from which to construct an Image object.
     * @throws IOException There is an error reading the body part's content.
     * @throws ContentException There is a <code>MessagingException</code> while
     *         creating this object. The <code>MessagingException</code> is wrapped
     *         within. It can be obtained by calling
     *         <code>ContentException.getWrappedException</code>.
     * @throws IllegalArgumentException if the input argument is null.
     */
    public Image( BodyPart bodyPart )
                  throws IOException, IllegalArgumentException, ContentException {
        super( bodyPart );
    }

    /**
     * Constructs an Image object from a File.
     *
     * @param file The image file.
     * @throws FileNotFoundException The specified file does not exist.
     * @throws ContentException There is a <code>MessagingException</code> while
     *         creating this object. The <code>MessagingException</code> is wrapped
     *         within. It can be obtained by calling
     *         <code>ContentException.getWrappedException</code>.
     * @throws IllegalArgumentException if the input argument is null.
     */
    public Image( File file ) throws FileNotFoundException,
                                     IllegalArgumentException,
                                     ContentException {
        super( file );
    }

}
