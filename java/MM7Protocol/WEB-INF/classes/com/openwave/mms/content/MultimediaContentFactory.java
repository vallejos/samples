// EDITOR NOTE: Please set number of columns to 100 in ur editor.

package com.openwave.mms.content;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;

import javax.mail.internet.MimeMultipart;

/**
 * This class creates concrete implementation objects for the 
 * <code>MultimediaContent</code interface.
 */

public class MultimediaContentFactory {

    /**
     * Private constructor to make sure that no one else can create instances
     * of this class. This class must be a singleton. Clients should get
     * instances of this class by calling the static getInstance method.
     */
    private MultimediaContentFactory() {
    }

    /**
     * Returns an instance of this class.
     *
     * @return An instance of this class.
     */
    public static MultimediaContentFactory getInstance() {
        return factory;
    }

    /**
     * Obtains a reference to a concrete implementation of the
     * <code>MultimediaContent</code> interface.
     *
     * @return An object implementing the <code>MultimediaContent</code> interface.
     */
    public MultimediaContent newMultimediaContent() {
        return new MultimediaContentImpl();
    }

    /**
     * Constructs a <code>MultimediaContent</code> object by reading
     * the input stream provided. It expects a MIME multipart formatted message
     * in the input stream. It looks for the MIME part that contains the SMIL
     * description of the content and constructs the <code>Slide</code>s accordingly.
     *
     * @param inputStream The input stream from which to read the multimedia content.
     * @exception IOException The input stream cannot be read.
     * @exception ContentException The multipart message does not contain a SMIL part
     *            or there is a <code>MessagingException</code> dealing with the
     *            MIME body parts.
     * @exception IllegalContentTypeException A subclass of <code>ContentException</code>
     *            is thrown if the content-type header in the MIME message is not
     *            multipart/related or it cannot be parsed.
     * @exception InvalidSmilException A subclass of <code>ContentException</code> is
     *            thrown if the SMIL cannot be parsed.
     * @exception SmilNotFoundException A subclass of <code>ContentException</code> is
     *            thrown if a SMIL part cannot be found in the multipart message.
     */
    public MultimediaContent newMultimediaContent(InputStream inputStream) throws ContentException, IOException {
        return new MultimediaContentImpl(inputStream);
    }

    /**
     * Constructs a <code>MultimediaContent</code> object from the
     * <code>jvax.mail.internet.MimeMultipart</code> object provided. It looks
     * for the MIME part that contains the SMIL description of the content and
     * constructs the <code>Slide</code>s accordingly.
     *
     * @param multipart The multipart from which to construct the multimedia content.
     * @exception IOException The multipart content cannot be read.
     * @exception ContentException The multipart does not contain a SMIL part
     *            or there is a <code>MessagingException</code> dealing with the
     *            MIME body parts.
     * @exception IllegalContentTypeException A subclass of <code>ContentException</code>
     *            is thrown if the content-type header in the MIME input stream is not
     *            multipart/related or it cannot be parsed.
     * @exception InvalidSmilException A subclass of <code>ContentException</code> is
     *            thrown if the SMIL cannot be parsed.
     * @exception SmilNotFoundException A subclass of <code>ContentException</code> is
     *            thrown if a SMIL part cannot be found in the multipart message.
     */
    public MultimediaContent newMultimediaContent(MimeMultipart multipart) throws ContentException, IOException {
        return new MultimediaContentImpl(multipart);
    }

    /**
     * Constructs a <code>MultimediaContent</code> object by reading the given file.
     * The file could be a directory, a SMIL file, or a zip file. If the file is
     * a directory, it lists the files in the directory, picks the first file with
     * an extension of .smil, and contructs the <code>MultimediaContent</code> object
     * from it. If the file is a SMIL file, it constructs the <code>MultimediaContent</code>
     * object as described by the SMIL file. If the file is a zip file, it treats it as a
     * directory and constructs the <code>MultimediaContent</code> object accordingly.
     *
     * @param file The name of the file.
     * @exception FileNotFoundException The file, or any other media file
     *            referenced by the SMIL file, is not found.
     * @exception IOException There is an error reading one of the files.
     */
    public MultimediaContent newMultimediaContent(File file) throws ContentException, IOException, FileNotFoundException {
        return new MultimediaContentImpl(file);
    }

    private static MultimediaContentFactory factory = new MultimediaContentFactory();

}
