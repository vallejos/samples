// EDITOR NOTE: Please set number of columns to 100 in ur editor.

package com.openwave.mms.content;

import java.util.Properties;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;

import javax.mail.internet.MimeMultipart;

/**
 * This class creates concrete implementation objects for the interfaces
 * in the API.
 *
 * <p>
 * Template characteristics can be defined in a properties file as follows:
 * </p>
 *
 * <ul>
 * <li> content.template.1.name = textontop
 * <li> content.template.1.text.top = 0
 * <li> content.template.1.text.left = 0
 * <li> content.template.1.text.width = 100%
 * <li> content.template.1.text.height = 70%
 * <li> content.template.1.text.fit = hidden
 * <li> content.template.1.image.top = 50%
 * <li> content.template.1.image.left = 0
 * <li> content.template.1.image.width = 100%
 * <li> content.template.1.image.height = 30%
 * <li> content.template.1.image.fit = hidden
 * <p></p>
 * <li> content.template.2.name = sidebyside
 * <li> content.template.2.image.top = 0
 * <li> content.template.2.image.left = 0
 * <li> content.template.2.image.width = 50%
 * <li> content.template.2.image.height = 100%
 * <li> content.template.2.image.fit = hidden
 * <li> content.template.2.text.top = 0
 * <li> content.template.2.text.left = 50%
 * <li> content.template.2.text.width = 50%
 * <li> content.template.2.text.height = 100%
 * <li> content.template.2.text.fit = hidden
 * </ul>
 *
 * <p>
 * The <code>initializeTemplateFactory</code> method can be used to initialize this class
 * with properties read from an application-specific properties file. Then
 * the application can use <code>getTemplate</code> to access the templates.
 * </p>
 */
public class Factory {

    /**
     * This is a private constructor so that no one else can create instances
     * of this class. It must be a singleton. Clients should get
     * instances of this class by calling the static getInstance method.
     */
    private Factory(){
    }

    /**
     * Returns an instance of this class.
     *
     * @return A <code>Factory</code> object
     */
    public static Factory getInstance() {
        return factory;
    }

    /**
     * Obtains a reference to a concrete implementation of the
     * <code>MultimediaContent</code> interface.
     *
     * @return A concrete implementation of the <code>MultimediaContent</code> interface.
     */
    public MultimediaContent newMultimediaContent() {
        return MultimediaContentFactory.getInstance().newMultimediaContent();
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
     *            or there is a <code>MessagingException</code> while dealing with the
     *            MIME body parts.
     * @exception IllegalContentTypeException A subclass of <code>ContentException</code>
     *            is thrown if the content-type header in the MIME message is not
     *            multipart/related or it cannot be parsed.
     * @exception InvalidSmilException A subclass of <code>ContentException</code> is
     *            thrown if the SMIL cannot be parsed.
     */
    public MultimediaContent newMultimediaContent(InputStream inputStream) throws ContentException, IOException {
        return MultimediaContentFactory.getInstance().newMultimediaContent(inputStream);
    }

    /**
     * Constructs a <code>MultimediaContent</code> object from the
     * <code>javax.mail.internet.MimeMultipart</code> object provided. It looks
     * for the MIME part that contains the SMIL description of the content and
     * constructs the <code>Slide</code>s accordingly.
     *
     * @param multipart The multipart message from which to construct the multimedia content.
     * @exception IOException The multipart content cannot be read.
     * @exception ContentException The multipart message does not contain a SMIL part
     *            or there is a <code>MessagingException</code> dealing with the
     *            MIME body parts.
     * @exception IllegalContentTypeException A subclass of <code>ContentException</code>
     *            is thrown if the content-type header in the MIME input stream is not
     *            multipart/related or it cannot be parsed.
     * @exception InvalidSmilException A subclass of <code>ContentException</code> is
     *            thrown if the SMIL cannot be parsed.
     */
    public MultimediaContent newMultimediaContent(MimeMultipart multipart) throws ContentException, IOException {
        return MultimediaContentFactory.getInstance().newMultimediaContent(multipart);
    }

    /**
     * Constructs a <code>MultimediaContent</code> object by reading the
     * given file. The file could be a directory, a SMIL file, or a zip file. If it is
     * a directory, it lists the files in the directory, picks the first file with
     * an extension .smil, and contructs the <code>MultimediaContent</code> object
     * from it. If it is a SMIL file, it constructs the <code>MultimediaContent</code>
     * object as described by the SMIL file. If it is a zip file, it treats it as a
     * directory and constructs the <code>MultimediaContent</code> object accordingly.
     *
     * @param file The name of the file.
     * @exception FileNotFoundException The file, or any other media file
     *            referenced by the SMIL file, is not found.
     * @exception IOException There is an error reading one of the files.
     */
    public MultimediaContent newMultimediaContent(File file) throws ContentException, FileNotFoundException, IOException {
        return MultimediaContentFactory.getInstance().newMultimediaContent(file);
    }

    /**
     * Obtains a reference to a concrete implementation of the
     * <code>Slide</code> interface.
     */
    public Slide newSlide() {
        return SlideFactory.getInstance().newSlide();
    }

    /**
     * Searches the template map and returns the instance whose name matches
     * the given name. It returns the default template if name is null.
     *
     * @param name The name of the template to return. This name has to exactly match
     *        the property content.template.n.name defined in the properties file.
     *        textontop is different from TextOnTop. Returns the default template if
     *        name is null.
     * @return The template whose name matches the given name.
     *         Returns null if the template of the given name is not found.
     */
    public Template getTemplate(String name) {
        return TemplateFactory.getInstance().getTemplate(name);
    }

    /**
     * Initializes the template factory with properties defining the templates.
     *
     * @param properties The properties defining the templates.
     * @exception NumberFormatException There is an error parsing the property values.
     */
    public void initializeTemplateFactory(Properties properties) throws NumberFormatException {
        TemplateFactory.getInstance().initialize(properties);
    }

    private static Factory factory = new Factory();

}