// EDITOR NOTE: Please set number of columns to 100 in ur editor.

package com.openwave.mms.content;

/**
 * This interface represents a template on which all slides in a multimedia
 * presentation are based. It contains two objects representing the attributes of
 * the text and image regions.
 */

public interface Template {

    /**
     * Sets the attributes of the text region of the template.
     *
     * @param textAttributes The attributes of the text region of the template.
     */
    public void setTextAttributes( RegionAttributes textAttributes );

    /**
     * Gets the attributes of the text region of the template.
     *
     * @return The attributes of the text region of the template.
     */
    public RegionAttributes getTextAttributes( );

    /**
     * Sets the attributes of the image region of the template.
     *
     * @param imageAttributes The attributes of the image region of the template.
     */
    public void setImageAttributes( RegionAttributes imageAttributes );

    /**
     * Gets the attributes of the image region of the template.
     *
     * @return The attributes of the image region of the template.
     */
    public RegionAttributes getImageAttributes( );

}
