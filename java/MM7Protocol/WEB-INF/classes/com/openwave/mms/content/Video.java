/**
 * Copyright (c) 2002-2003 Openwave Systems Inc. All rights reserved.
 *
 * The copyright to the computer software herein is the property of
 * Openwave Systems Inc. The software may be used and/or copied only
 * with the written permission of Openwave Systems Inc. or in accordance
 * with the terms and conditions stipulated in the agreement/contract
 * under which the software has been supplied.
 *
 * $Id: Video.java,v 1.1 2007/02/20 16:01:46 cvsuser Exp $
 */

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
 * This class represents a media object of type video.
 */

public final class Video extends MediaObject {

    /**
     * The default constructor.
     */
    public Video() {
    }

    /**
     * Constructs a Video object from an existing JavaMail BodyPart.
     *
     * @param bodyPart The body part from which to construct an Video object.
     * @throws IOException There is an error reading the body part's content.
     * @throws ContentException There is a <code>MessagingException</code> while
     *         creating this object. The <code>MessagingException</code> is wrapped
     *         within. It can be obtained by calling
     *         <code>ContentException.getWrappedException</code>.
     * @throws IllegalArgumentException if the input argument is null.
     */
    public Video( BodyPart bodyPart )
                  throws IOException, IllegalArgumentException, ContentException {
        super( bodyPart );
    }

    /**
     * Constructs an Video object from a File.
     *
     * @param file The video file.
     * @throws FileNotFoundException The specified file does not exist.
     * @throws ContentException There is a <code>MessagingException</code> while
     *         creating this object. The <code>MessagingException</code> is wrapped
     *         within. It can be obtained by calling
     *         <code>ContentException.getWrappedException</code>.
     * @throws IllegalArgumentException if the input argument is null.
     */
    public Video( File file ) throws FileNotFoundException,
                                     IllegalArgumentException,
                                     ContentException {
        super( file );
    }

}
