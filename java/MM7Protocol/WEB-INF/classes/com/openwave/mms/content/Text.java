// EDITOR NOTE: Please set number of columns to 100 in ur editor.

package com.openwave.mms.content;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.UnsupportedEncodingException;

import javax.mail.BodyPart;
import javax.mail.MessagingException;
import javax.mail.internet.MimeBodyPart;
import javax.mail.internet.MimeUtility;

/**
 * This class encapsulates the text associated with a slide.
 */

public final class Text extends MediaObject {

    /**
     * The default constructor.
     */
    public Text() {
    }

    /**
     * Constructs a <code>Text</code> object from an existing JavaMail
     * <code>BodyPart</code>.
     *
     * @param bodyPart The body part from which to construct a <code>Text</code> object.
     * @throws IOException There is an error reading the body part's content.
     * @throws ContentException There is a <code>MessagingException</code> while
     *         creating the object. The <code>MessagingException</code> is wrapped
     *         within. It can be obtained by calling
     *         <code>ContentException.getWrappedException</code>.
     * @throws IllegalArgumentException The input argument is null.
     */
    public Text( BodyPart bodyPart ) 
                 throws IOException, IllegalArgumentException, ContentException {
        super( bodyPart );
    }

    /**
     * Constructs a Text object from a File.
     *
     * @param file The text file.
     * @throws FileNotFoundException The specified file does not exist.
     * @throws ContentException There is a <code>MessagingException</code> while
     *         creating this object. The <code>MessagingException</code> is wrap
ped
     *         within. It can be obtained by calling
     *         <code>ContentException.getWrappedException</code>.
     * @throws IllegalArgumentException if the input argument is null.
     */
    public Text( File file ) throws FileNotFoundException,
                                    IllegalArgumentException,
                                    ContentException {
        super( file );
    }

    /**
     * Constructs a <code>Text</code> object by reading a text file.
     *
     * @param file The text file.
     * @param charset The charset of the text in the file. If null, it assumes the
     *        platform default charset.
     * @throws FileNotFoundException The specified file does not exist.
     * @throws IOException The specified file cannot be read.
     * @throws UnsupportedEncodingException The specified charset is not supported.
     * @throws ContentException There is a <code>MessagingException</code> while
     *         creating the object. The <code>MessagingException</code> is wrapped
     *         within. It can be obtained by calling
     *         <code>ContentException.getWrappedException</code>.
     * @throws IllegalArgumentException The input argument is null.
     */
    public Text( File file, String charset ) throws FileNotFoundException,
                                                    UnsupportedEncodingException,
                                                    IOException,
                                                    IllegalArgumentException,
                                                    ContentException {
        if( file == null ) {
            throw new IllegalArgumentException( "input file argument is null" );
        }
        if( file.exists() == true ) {
            String defaultCharset = MimeUtility.mimeCharset( MimeUtility.getDefaultJavaCharset() );
            InputStreamReader reader = new InputStreamReader(
                                           new FileInputStream( file ),
                                           charset == null ? defaultCharset
                                                           : charset );
            char[] bytes = new char[1024];
            StringBuffer buffer = new StringBuffer( 1024 );
            int bytesRead = 0;
            while( ( bytesRead = reader.read( bytes, 0, 1024 ) ) != -1 ) {
                buffer.append( bytes, 0, bytesRead );
            }
            try {
                super.setText( buffer.toString(), charset );
                super.setFileName( file.getName() );
            } catch( MessagingException me ) {
                throw new ContentException( "messaging-exception",
                                            me );
            }
        } else {
            throw new FileNotFoundException( "file " + file.getPath() + " not found" );
        }
    }

    /**
     * Constructs a <code>Text</code> object from a string.
     *
     * @param text The content of the object.
     * @param charset The charset of the string. If it is null, the default platform
     *        charset is used.
     * @throws ContentException There is a <code>MessagingException</code> while
     *         creating the object. The <code>MessagingException</code> is wrapped
     *         within. It can be obtained by calling
     *         <code>ContentException.getWrappedException</code>.
     */
    public Text( String text, String charset ) throws ContentException {
        try {
            super.setText( text, charset );
        } catch( MessagingException me ) {
            throw new ContentException( "messaging-exception",
                                        me );
        }
    }

}
