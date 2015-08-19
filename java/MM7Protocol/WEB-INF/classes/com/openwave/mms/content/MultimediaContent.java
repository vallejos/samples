/**
 * Copyright (c) 2002-2003 Openwave Systems Inc. All rights reserved.
 *
 * The copyright to the computer software herein is the property of
 * Openwave Systems Inc. The software may be used and/or copied only
 * with the written permission of Openwave Systems Inc. or in accordance
 * with the terms and conditions stipulated in the agreement/contract
 * under which the software has been supplied.
 *
 * $Id: MultimediaContent.java,v 1.1 2007/02/20 16:01:46 cvsuser Exp $
 */

package com.openwave.mms.content;

import java.awt.Dimension;

import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;

import java.util.List;

import javax.mail.MessagingException;
import javax.mail.internet.MimeMultipart;

/**
 * This interface represents a Multimedia Presentation consisting of slides. Each
 * object of this type consists of a list of <code>Slide</code>s which corresponds
 * to a "par" element in SMIL. Each <code>Slide</code> object in turn contains the actual
 * multimedia objects, like <code>Video>, <code>Image</code>, <code>Audio</code>,
 * and <code>Text</code>. A <code>MutltimediaContent</code> object also contains
 * a <code>Template</code> object which specifies the coordinates of regions on
 * the screen where the different multimedia objects (only <code>Text</code>
 * <code>Video</code> and <code>Image</code>, in this case) are to be rendered.
 */

public interface MultimediaContent {

    /**
     * Adds a slide to the list of slides in the object.
     *
     * @param slide The slide to be added.
     */
    public void addSlide( Slide slide );

    /**
     * Adds a slide to the list of slides in the object at the specified position.
     *
     * @param slide The slide to be added.
     * @param index The position at which to add the slide.
     */
    public void addSlide( Slide slide, int index );

    /**
     * Removes a slide at the given position in the list.
     *
     * @param slideNumber The position of the slide in the list.
     * @exception IndexOutOfBoundsException There are not that many slides
     *            in the object.
     */
    public void removeSlide( int slideNumber ) throws IndexOutOfBoundsException;
 
    /**
     * Removes a slide.
     *
     * @param slide The slide to be removed.
     */
    public void removeSlide( Slide slide );
 
    /**
     * Creates a new <code>Slide</code> object, adds it to the list of slides in the object
     * and returns the slide.
     *
     * @return The newly created slide.
     */
    public Slide newSlide();

    /**
     * Creates a new Slide object, adds it to the list of slides in the object,
     * and returns the slide.
     *
     * @param index The index at which to add the slide. Adds the slide at the end of the list
     *        if the index exceeds the number of slides in the object.
     * @return The newly created slide.
     */
    public Slide newSlide( int index );

    /**
     * Writes the contents of this object to an output stream. It generates
     * the SMIL corresponding to the contents, creates a MIME multipart message
     * with the SMIL and the media objects, and writes it to the output stream.
     *
     * @param outputStream The output stream to write to.
     * @exception ContentException The MIME multipart message cannot be created.
     * @exception IOException There is an error writing output to the stream.
     */
    public void writeTo( OutputStream outputStream )
                         throws ContentException, IOException;

    /**
     * Gets the content as a JavaMail <code>MimeMultipart</code> object.
     * It creates the SMIL corresponding to its contents, generates a
     * <code>MimeMultipart</code> object containing the SMIL and the media
     * objects, and returns it.
     *
     * @exception ContentException There is an error synthesizing SMIL.
     * @exception MessagingException The <code>MimeMultipart</code> object cannot be created.
     * @return A <code>MimeMultipart</code> object containing the SMIL and the media objects.
     */
    public Object getContent() throws ContentException, MessagingException;

    /**
     * Returns the slides contained in the object as a list.
     *
     * @return The list of <code>Slide</code>s.
     */
    public List getSlides();

    /**
     * Sets the slides of the object.
     *
     * @param slides A list of <code>Slide</code>s. The existing slides will be replaced
     *        by this list.
     */
    public void setSlides( List slides );

    /**
     * Returns the slide at the given position.
     *
     * @param slideNumber The position of the slide in the List.
     * @return The <code>Slide</code> object indexed by the slideNumber.
     * @exception IndexOutOfBoundsException The list does not contain that many elements.
     */
    public Slide getSlide( int slideNumber );

    /**
     * Returns the number of slides in the object.
     *
     * @return The number of slides in the object.
     */
    public int getNumSlides();

    /**
     * Removes all the slides in the object.
     */
    public void clear();

    /**
     * Sets the template for the slides in the object.
     *
     * @param template The template to be used for the slides in the object.
     */
    public void setTemplate( Template template );

    /**
     * Returns the template associated with the slides in the object.
     *
     * @return The template associated with the slides in the object.
     */
    public Template getTemplate();

    /**
     * Sets the size of the viewport in which the SMIL presentation
     * is rendered.
     *
     * @param viewportSize The size of the viewport.
     */
    public void setViewportSize( Dimension viewportSize );

    /**
     * Returns the viewport size.
     *
     * @return The size of the viewport.
     */
    public Dimension getViewportSize();

    /**
     * Checks if the content contained in the object has a SMIL description
     * associated with it. When the object is constructed using the constructors
     * which take the input stream or a multipart message, this method tells you
     * whether the content has a SMIL part.
     *
     * @return True if the content is SMIL based, false otherwise.
     */
    public boolean isSmilBased();

}
