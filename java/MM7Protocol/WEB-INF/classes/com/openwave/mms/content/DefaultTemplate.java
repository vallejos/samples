// EDITOR NOTE: Please set number of columns to 100 in ur editor.

package com.openwave.mms.content;

/**
 * This class implements the <code>Template</code> interface to define a default
 * version of a template. This implementation returns:
 * <ul>
 * <li> For Image region: top = 0, left = 0, height = 50%, width = 100%, fit = hidden
 * <li> For Text region: top = 50%, left = 0, height = 50%, width = 100%, fit = hidden
 * </ul>
 */

public class DefaultTemplate implements Template {

    /**
     * The default constructor creates region attributes for both image and text
     * regions as follows:
     * <ul>
     * <li> For Image region: top = 0, left = 0, height = 50%, width = 100%, fit = hidden
     * <li> For Text region: top = 50%, left = 0, height = 50%, width = 100%, fit = hidden
     * </ul>
     */
    public DefaultTemplate() {
        textAttributes = new RegionAttributes();
        textAttributes.setTop( DEFAULT_TEXT_TOP );
        textAttributes.setLeft( DEFAULT_TEXT_LEFT );
        textAttributes.setHeight( DEFAULT_TEXT_HEIGHT );
        textAttributes.setWidth( DEFAULT_TEXT_WIDTH );
        textAttributes.setFit( DEFAULT_TEXT_FIT );

        imageAttributes = new RegionAttributes();
        imageAttributes.setTop( DEFAULT_IMAGE_TOP );
        imageAttributes.setLeft( DEFAULT_IMAGE_LEFT );
        imageAttributes.setHeight( DEFAULT_IMAGE_HEIGHT );
        imageAttributes.setWidth( DEFAULT_IMAGE_WIDTH );
        imageAttributes.setFit( DEFAULT_IMAGE_FIT );
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
    public RegionAttributes getTextAttributes() {
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
    public RegionAttributes getImageAttributes() {
        return imageAttributes;
    }

    private RegionAttributes textAttributes;
    private RegionAttributes imageAttributes;

    private static final String DEFAULT_IMAGE_TOP    = "0";
    private static final String DEFAULT_IMAGE_LEFT   = "0";
    private static final String DEFAULT_IMAGE_HEIGHT = "50%";
    private static final String DEFAULT_IMAGE_WIDTH  = "100%";
    private static final Fit    DEFAULT_IMAGE_FIT    = Fit.HIDDEN;

    private static final String DEFAULT_TEXT_TOP     = "50%";
    private static final String DEFAULT_TEXT_LEFT    = "0";
    private static final String DEFAULT_TEXT_HEIGHT  = "50%";
    private static final String DEFAULT_TEXT_WIDTH   = "100%";
    private static final Fit    DEFAULT_TEXT_FIT     = Fit.HIDDEN;
}
