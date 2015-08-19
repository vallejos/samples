/**
 * Copyright (c) 2003 Openwave Systems Inc. All rights reserved.
 *
 * The copyright to the computer software herein is the property of
 * Openwave Systems Inc. The software may be used and/or copied only
 * with the written permission of Openwave Systems Inc. or in accordance
 * with the terms and conditions stipulated in the agreement/contract
 * under which the software has been supplied.
 *
 * $Id: Slide.java,v 1.1 2007/02/20 16:01:46 cvsuser Exp $
 */

package com.openwave.mms.content;

import java.util.Enumeration;
import java.util.Set;

import javax.mail.internet.MimeBodyPart;

/**
 * This interface defines the behavior of a <code>Slide</code> which
 * roughly corresponds to a "par" element in SMIL. A default implementation
 * of this interface, which can be obtained from <code>SlideFactory</code>,
 * is also available in the API. A <code>Slide</code> acts as a container
 * for a set of <code>Text</code>, <code>Image</code>, <code>Audio</code>
 * and <code>Video</code> objects. As per the OMA MMS Conformance document,
 * a <code>Slide</code> can contain either a <code>Video</code> or
 * an <code>Image</code> and/or <code>Audio</code>.
 */

public interface Slide {

    /**
     * Sets the video to be used in the slide. A null value has the effect
     * of removing the video associated with the slide.
     *
     * @param video The video for the slide.
     */
    public void setVideo( Video video );

    /**
     * Returns the video associated with the slide.
     *
     * @return The video associated with the slide.
     */
    public Video getVideo();

    /**
     * Sets the image to be used in the slide. A null value has the effect
     * of removing the image associated with the slide.
     *
     * @param image The image for the slide.
     */
    public void setImage( Image image );

    /**
     * Returns the image associated with the slide.
     *
     * @return The image associated with the slide.
     */
    public Image getImage();

    /**
     * Sets the audio to be used in the slide. A null value has the effect
     * of removing the audio associated with the slide.
     *
     * @param audio The audio for the slide.
     */
    public void setAudio( Audio audio );

    /**
     * Returns the audio associated with the slide.
     *
     * @return The audio associated with the slide.
     */
    public Audio getAudio();

    /**
     * Sets the text to be used in the slide. A null value has the effect
     * of removing the text associated with the slide.
     *
     * @param text The text for the slide.
     */
    public void setText( Text text );

    /**
     * Returns the text associated with the slide.
     *
     * @return The text associated with the slide.
     */
    public Text getText();

    /**
     * Sets the duration of the slide. The value passed should be in 
     * milliseconds.
     *
     * @param duration The duration of the slide in milliseconds.
     */
    public void setDuration( int duration );

    /**
     * Sets the duration of the slide. The input string can optionally
     * end in "ms", "s", "min", or "h". The default is ms.
     *
     * @param duration The duration of the slide.
     * @throws NumberFormatException The duration does not contain a number.
     */
    public void setDuration( String duration ) throws NumberFormatException;

    /**
     * Returns the duration of the slide in milliseconds.
     *
     * @return The duration of the slide in milliseconds.
     */
    public int getDuration();

    /**
     * Returns the combined size of the media objects associated with the slide.
     */
    public int getSize();

    /**
     * Gets the media objects associated with the slide as a
     * <code>java.util.Set</code> of <code>MediaObject</code>s.
     */
    public Set getMediaObjects();

}
