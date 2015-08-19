// EDITOR NOTE: Please set number of columns to 100 in ur editor.

package com.openwave.mms.content;

/**
 * This interface represents a template on which all Slides are based. It
 * has two objects representing the attributes of the Text and Image regions.
 */

final class TemplateImpl implements Template {

    /**
     * Constructor
     *
     * @param textAttributes The attributes of the text region of the template.
     * @param imageAttributes The attributes of the image region of the template.
     */
    public TemplateImpl( RegionAttributes textAttributes,
                         RegionAttributes imageAttributes ) {
        this.textAttributes = textAttributes;
        this.imageAttributes = imageAttributes;
    }

    /**
     * Sets the attributes of the text region of the template.
     *
     * @param textAttributes The attributes of the text region of the template.
     */
    public void setTextAttributes( RegionAttributes textAttributes ) {
        this.textAttributes = textAttributes;
    }

    /**
     * Gets the attributes of the text region of the template.
     *
     * @return The attributes of the text region of the template.
     */
    public RegionAttributes getTextAttributes( ) {
        return textAttributes;
    }

    /**
     * Sets the attributes of the image region of the template.
     *
     * @param imageAttributes The attributes of the image region of the template.
     */
    public void setImageAttributes( RegionAttributes imageAttributes ) {
        this.imageAttributes = imageAttributes;
    }

    /**
     * Gets the attributes of the image region of the template.
     *
     * @return The attributes of the image region of the template.
     */
    public RegionAttributes getImageAttributes( ) {
        return imageAttributes;
    }

    private RegionAttributes textAttributes;
    private RegionAttributes imageAttributes;

}
