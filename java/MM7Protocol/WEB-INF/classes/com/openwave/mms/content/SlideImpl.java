/**
 * Copyright (c) 2002-2003 Openwave Systems Inc. All rights reserved.
 *
 * The copyright to the computer software herein is the property of
 * Openwave Systems Inc. The software may be used and/or copied only
 * with the written permission of Openwave Systems Inc. or in accordance
 * with the terms and conditions stipulated in the agreement/contract
 * under which the software has been supplied.
 *
 * $Id: SlideImpl.java,v 1.2 2007/03/02 15:56:10 anavarro Exp $
 */

package com.openwave.mms.content;

import java.io.ByteArrayOutputStream;
import java.io.IOException;

import java.util.HashSet;
import java.util.Set;

import javax.mail.MessagingException;
import javax.mail.internet.MimeBodyPart;

/**
 * This class provides a default implementation for the Slide interface.
 */

final class SlideImpl implements Slide {

    /**
     * The constructor.
     */
    public SlideImpl() {
        changed = true;
        size = -1;
    }

    /**
     * Sets the Video to be used in the slide. A null
     * can be passed in to remove the video associated with the slide.
     *
     * @param video The video for the slide.
     * @exception IllegalStateException is there is already an Image and/or
     *            audio associated with this slide.
     */
    public void setVideo(Video video) {
        if (image != null || audio != null) {
            throw new IllegalStateException("slide already has image and/or audio associated with it");
        }
        changed = true;
        this.video = video;
    }

    /**
     * Returns the video associated with the slide.
     *
     * @return The video associated with the slide.
     */
    public Video getVideo() {
        return video;
    }

    /**
     * Sets the Image to be used in the slide. A null
     * can be passed in to remove the image associated with the slide.
     *
     * @param image The image for the slide.
     * @exception IllegalStateException if the slide already has a Video
     *            associated with it.
     */
    public void setImage(Image image) {
        if (video != null) {
            throw new IllegalStateException("slide already has a video associated with it");
        }
        changed = true;
        this.image = image;
    }

    /**
     * Returns the image associated with the slide.
     *
     * @return The image associated with the slide.
     */
    public Image getImage() {
        return image;
    }

    /**
     * Sets the Audio to be used in the slide. A null
     * can be passed in to remove the audio associated with this slide.
     *
     * @param audio The audio for the slide.
     * @exception IllegalStateException if the slide already has a Video
     *            associated with it.
     */
    public void setAudio(Audio audio) {
        if (video != null) {
            throw new IllegalStateException("slide already has a video associated with it");
        }
        changed = true;
        this.audio = audio;
    }

    /**
     * Returns the audio associated with the slide.
     *
     * @return The audio associated with the slide.
     */
    public Audio getAudio() {
        return audio;
    }

    /**
     * Sets the Text to be used in the slide. A null
     * can be passed in to remove the text associated with the slide.
     *
     * @param text The text for the slide.
     */
    public void setText(Text text) {
        changed = true;
        this.text = text;
    }

    /**
     * Returns the Text associated with the slide.
     *
     * @return The text associated with the slide.
     */
    public Text getText() {
        return text;
    }

    /**
     * Sets the duration of the slide. The value passed
     * should be in milliseconds.
     *
     * @param duration The duration of the slide in milliseconds.
     */
    public void setDuration( int duration ) {
        this.duration = duration;
    }

    /**
     * Sets the duration of the slide. The input string
     * can optionally end in "ms", "s", "min", or "h". The default is ms.
     *
     * @param duration The duration of the slide.
     */
    public void setDuration( String duration ) throws NumberFormatException {

        if( duration == null || duration.length() == 0 ) {
            this.duration = 0;
            return;
        }

        int multiplier = 1;
        if( duration.endsWith( "ms" ) ) {
            duration = duration.substring( 0, duration.length() - 2 );
        } else if( duration.endsWith( "s" ) ) {
            duration = duration.substring( 0, duration.length() - 1 );
            multiplier = 1000;
        } else if( duration.endsWith( "min" ) ) {
            duration = duration.substring( 0, duration.length() - 3 );
            multiplier = 60*1000;
        } else if( duration.endsWith( "h" ) ) {
            duration = duration.substring( 0, duration.length() - 1 );
            multiplier = 60*60*1000;
        }

        // we should be able to read float value although the spec says
        // duration will be specified in integer millisecs.
        int dur = Float.valueOf( duration ).intValue() * multiplier;

        this.duration = dur;
    }

    /**
     * Returns the duration of the slide in milliseconds.
     *
     * @return The duration of the slide in milliseconds.
     */
    public int getDuration() {
        return duration;
    }

    /**
     * Returns the combined size of the media objects
     * associated with the slide.
     */
    public int getSize() {
        if (changed == true) {
            try {
                ByteArrayOutputStream baos = new ByteArrayOutputStream(4096);
                
                if (text != null) {
                    text.writeTo(baos);
                }
                if (video != null) {
                    video.writeTo(baos);
                }
                if (image != null) {
                    image.writeTo(baos);
                }
                if (audio != null) {
                    audio.writeTo(baos);
                }
    
                size = baos.size();
                
            }catch(Exception e) {
                return -1;
            }
        }

        return size;
    }

    /**
     * Returns the media objects associated with the
     * slide as a Set of MediaObject objects.
     */
    public Set getMediaObjects() {
        Set mediaObjects = new HashSet();
        if( text != null ) {
            mediaObjects.add( text );
        }
        if( video != null ) {
            mediaObjects.add( video );
        }
        if( image != null ) {
            mediaObjects.add( image );
        }
        if( audio != null ) {
            mediaObjects.add( audio );
        }

        return mediaObjects;
    }

    private Video video;
    private Image image;
    private Audio audio;
    private Text text;
    private int duration; // in milliseconds
    private boolean changed;
    private int size;

}
