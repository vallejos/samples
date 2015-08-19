// EDITOR NOTE: Please set number of columns to 100 in ur editor.

package com.openwave.mms.content;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;

import java.util.Enumeration;

import javax.mail.BodyPart;
import javax.mail.Header;
import javax.mail.MessagingException;
import javax.mail.internet.ContentType;
import javax.mail.internet.InternetHeaders;
import javax.mail.internet.MimeBodyPart;
import javax.mail.internet.MimeMultipart;
import javax.mail.internet.MimeUtility;

import javax.activation.DataHandler;
import javax.activation.FileDataSource;

import com.openwave.mms.content.drm.Rights;

/**
 * This class is the super class of all media objects that can be attached to a slide.
 */

public class MediaObject extends MimeBodyPart {

    /**
     * The default constructor.
     */
    public MediaObject() {
        forwardLock = false;
    }

    /**
     * Constructs a <code>MediaObject</code> object from a JavaMail <code>BodyPart</code>.
     *
     * @param bodyPart The body part from which to construct a <code>MediaObject</code>.
     * @throws IOException There is an error reading the body part's content.
     * @throws ContentException There is a <code>MessagingException</code> while
     *         creating this object. The <code>MessagingException</code> is wrapped
     *         within. It can be obtained by calling
     *         <code>ContentException.getWrappedException</code>.
     * @throws IllegalArgumentException The input argument is null.
     */
    public MediaObject( BodyPart bodyPart ) throws IOException,
                                                   IllegalArgumentException,
                                                   ContentException {
        if( bodyPart == null ) {
            throw new IllegalArgumentException( "input body part is null" );
        }

        try {
            // unwrap the drm wrapper if it is present
            Object content = bodyPart.getContent();
            ContentType contentType = new ContentType( bodyPart.getContentType() );
            if( content instanceof MimeMultipart &&
                contentType.match( "application/vnd.oma.drm.message" ) ) {
                // drm encoded media object
                forwardLock = true;
                MimeMultipart drmWrapper = (MimeMultipart) content;
                if( drmWrapper.getCount() == 1 ) {
                    bodyPart = drmWrapper.getBodyPart(0);
                } else if( drmWrapper.getCount() == 2 ) {
                    // the oma drm spec says that the first part MUST
                    // be the rights object and the second part media
                    rights = new Rights( ( String ) drmWrapper.getBodyPart(0).getContent() );
                    bodyPart = drmWrapper.getBodyPart(1);
                } else {
                    // malformed DRM message
                    throw new MalformedDRMContentException( "malformed-drm-content" );
                }
            }

            // create the media object
            Enumeration headers = bodyPart.getAllHeaders();
            while( headers.hasMoreElements() ) {
                Header header = ( Header ) headers.nextElement();
                setHeader( header.getName(), header.getValue() );
            }
            super.setContent( bodyPart.getContent(), bodyPart.getContentType() );
            super.setDataHandler( bodyPart.getDataHandler() );
        } catch( MessagingException me ) {
            throw new ContentException( "messaging-exception",
                                        me );
        }
    }

    /**
     * This constructor takes a <code>java.io.File</code> object and constructs a
     * <code>MediaObject</code>.
     *
     * @param file The media file.
     * @throws FileNotFoundException The specified file does not exist.
     * @throws ContentException There is a <code>MessagingException</code> while
     *         creating this object. The <code>MessagingException</code> is wrapped
     *         within. It can be obtained by calling
     *         <code>ContentException.getWrappedException</code>.
     * @throws IllegalArgumentException The input argument is null.
     */
    public MediaObject( File file ) throws FileNotFoundException,
                                           IllegalArgumentException,
                                           ContentException {
        if( file == null ) {
            throw new IllegalArgumentException( "input file argument is null" );
        }
        if( file.exists() == true ) {
            try {
                super.setDataHandler( new DataHandler( new FileDataSource( file ) ) );
                super.setFileName( file.getName() );
                super.setContentID( "<" + file.getName() + ">" );
            } catch( MessagingException me ) {
                throw new ContentException( "messaging-exception",
                                            me );
            }
        } else {
            throw new FileNotFoundException( "file " + file.getName() + " not found" );
        }
    }

    /**
     * Saves the contents of the media object to the file system.
     * The contents are stored in the given directory. If directory
     * is null, it creates the file in the current directory. If the
     * media object has a file name associated with it, the file name is 
     * used. Otherwise, a file name is created using
     * <code>File.createTempFile</code> with a prefix of "mms" and
     * no suffix.
     *
     * @param directory The directory in which to store the file.
     * @throws IOException The file cannot be written.
     * @throws ContentException There is a <code>MessagingException</code> while
     *         reading the content of this object. The <code>MessagingException</code>
     *         is wrapped within. It can be obtained by calling
     *         <code>ContentException.getWrappedException</code>.
     */
    public void save( File directory ) throws IOException,
                                              ContentException {
        try {
            String fileName = getFileName();
            File file = null;
            if( fileName == null || fileName.length() == 0 ) {
                fileName = getContentID();
                if( fileName == null || fileName.length() == 0 ) {
                    file = File.createTempFile( "mms", "", directory );
                } else {
                    fileName = fileName.substring( 1, fileName.length() - 1 );
                    file = new File( directory, fileName );
                }
            } else {
                file = new File( directory, fileName );
            }
            FileOutputStream outputStream = new FileOutputStream( file );
            super.getDataHandler().writeTo( outputStream );
            outputStream.flush();
        } catch( MessagingException me ) {
            throw new ContentException( "messaging-exception",
                                        me );
        }
    }

    /**
     * Sets an alternate string for the media object so that user agents
     * that cannot display it can display the string instead.
     *
     * @param alt The string to be used if the user agent cannot display the
     *        media.
     */
    public void setAlt( String alt ) {
        this.alt = alt;
    }

    /**
     * Gets the alt string associated with the media object. If the user 
     * agent cannot display the media, this string can be displayed instead.
     *
     * @return The alt string to be used in place of the media object.
     */
    public String getAlt() {
        return alt;
    }

    /**
     * Sets the time at which the media object is played.
     *
     * @param begin The time, in milliseconds, at which the media object
     *        should be played.
     */
    public void setBegin( int begin ) {
        this.begin = begin;
    }

    /**
     * Sets the time at which the media object is played. 
     * The paramenter <code>begin</code> can optionally end in
     * "ms", "s", "min", or "h". The default is ms.
     *
     * @param begin The time at which the media object should be played.
     * @throws NumberFormatException The string does not contain an
     *         integer.
     */
    public void setBegin( String begin ) throws NumberFormatException {
        if( begin != null && begin.length() > 0 ) {
            int multiplier = 1;
            if( begin.endsWith( "ms" ) ) {
                begin = begin.substring( 0, begin.length() - 2 );
            } else if( begin.endsWith( "s" ) ) {
                begin = begin.substring( 0, begin.length() - 1 );
                multiplier = 1000;
            } else if( begin.endsWith( "min" ) ) {
                begin = begin.substring( 0, begin.length() - 3 );
                multiplier = 60*1000;
            } else if( begin.endsWith( "h" ) ) {
                begin = begin.substring( 0, begin.length() - 1 );
                multiplier = 60*60*1000;
            }
            this.begin = Integer.parseInt( begin ) * multiplier;
        } else {
            this.begin = 0;
        }
    }

    /**
     * Returns the begin time of the media object in milliseconds.
     *
     * @return The begin time of the media object in milliseconds.
     */
    public int getBegin() {
        return begin;
    }

    /**
     * Sets the time at which the media object is stopped.
     *
     * @param end The time, in milliseconds, at which the media object
     *        should be stopped.
     */
    public void setEnd( int end ) {
        this.end = end;
    }

    /**
     * Sets the time at which the media object is stopped.
     * The parameter <code>end</code> can optionally end in
     * "ms", "s", "min", or "h". The default is ms.
     *
     * @param end The time at which the media object should be stopped.
     * @throws NumberFormatException The string does not contain an
     *         integer.
     */
    public void setEnd( String end ) throws NumberFormatException {
        if( end != null && end.length() > 0 ) {
            int multiplier = 1;
            if( end.endsWith( "ms" ) ) {
                end = end.substring( 0, end.length() - 2 );
            } else if( end.endsWith( "s" ) ) {
                end = end.substring( 0, end.length() - 1 );
                multiplier = 1000;
            } else if( end.endsWith( "min" ) ) {
                end = end.substring( 0, end.length() - 3 );
                multiplier = 60*1000;
            } else if( end.endsWith( "h" ) ) {
                end = end.substring( 0, end.length() - 1 );
                multiplier = 60*60*1000;
            }
            this.end = Integer.parseInt( end ) * multiplier;
        } else {
            this.end = 0;
        }
    }

    /**
     * Returns the end time of the media object in milliseconds.
     *
     * @return The end time of the media object in milliseconds.
     */
    public int getEnd() {
        return end;
    }

    /**
     * Applies a forward lock on this media object.
     * This ensures that the media object is wrapped in an OMA
     * DRM message format so that devices that support OMA DRM
     * will not allow users to forward this media object.
     */
    public void setForwardLock() {
        forwardLock = true;
    }

    /**
     * Returns the forward lock status of this media object.
     *
     * @return True if the object is forward locked, false otherwise.
     */
    public boolean getForwardLock() {
        return forwardLock;
    }

    /**
     * Returns the rights object associated with this media object.
     *
     * @return The rights object associated with this media object.
     */
    public Rights getRights() {
        return rights;
    }

    /**
     * Associates a DRM rights object with the media object.
     *
     * @param rights The DRM rights object.
     */
    public void setRights( Rights rights ) {
        this.rights = rights;
    }
     

    /**
     * Wraps this media object in the OMA DRM forward-lock message format.
     */
    MimeBodyPart drmEncode() throws MessagingException {

        MimeMultipart drmWrapper = new MimeMultipart();
        ContentType ct = new ContentType( drmWrapper.getContentType() );
        ct.setPrimaryType( "application" );
        ct.setSubType( "vnd.oma.drm.message" );

        if( rights != null ) {
            InternetHeaders headers = new InternetHeaders();
            headers.addHeader( "Content-Type", "application/vnd.oma.drm.rights+xml" );
            MimeBodyPart rights = new MimeBodyPart( headers, this.rights.toString().getBytes() );
            drmWrapper.addBodyPart( rights );
        }
        drmWrapper.addBodyPart( this );

        MimeBodyPart retval = new MimeBodyPart();
        retval.setContent( drmWrapper );
        retval.addHeader( "Content-Type", ct.toString() );
        retval.addHeader( "Content-ID", getContentID() );

        return retval;
    }

    private String alt;
    private int begin;
    private int end;
    private boolean forwardLock;
    private Rights rights;

}
