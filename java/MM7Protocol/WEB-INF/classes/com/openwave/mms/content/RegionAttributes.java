// EDITOR NOTE: Please set number of columns to 100 in ur editor.

package com.openwave.mms.content;

import com.openwave.mms.content.smil.Region;

/**
 * This class represents the attributes of a SMIL region.
 */
public final class RegionAttributes {

    /**
     * Default constructor that constructs an empty object.
     */
    public RegionAttributes() {}

    /**
     * Convenience constructor that constructs from a SMIL region.
     */
    RegionAttributes( Region region ) throws NumberFormatException {
        this.top = IntOrPercent.valueOf( region.getTop() );
        this.left = IntOrPercent.valueOf( region.getLeft() );
        this.width = IntOrPercent.valueOf( region.getWidth() );
        this.height = IntOrPercent.valueOf( region.getHeight() );
        this.fit = Fit.valueOf( region.getFit() );
    }

    /**
     * Convenience method that checks if the attributes match a SMIL region's attributes.
     */
    boolean equals(Region region) {
    	return equals(top, region.getTop()) &&
               equals(left, region.getLeft()) &&
               equals(width, region.getWidth()) &&
               equals(height, region.getHeight()) &&
               equals(fit, region.getFit());
    }

    /**
     * Compares the two values after checking for null.
     * The first parameter type is Object to allow use
     * with both IntOrPercent and Fit objects.
     */
    private static boolean equals( Object value1, String value2 ) {
        if( value1 != null && value2 != null ) {
            return value1.toString().equalsIgnoreCase( value2 );
        } else if( value1 == null && value2 == null ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Sets the value for the attribute top.
     *
     * @param top The value of the attribute. It can contain a
     *        '%' sign at the end to denote the value in percentage.
     * @exception NumberFormatException The value does not contain a number.
     */
    public void setTop( String top ) throws NumberFormatException {
        this.top = new IntOrPercent( top );
    }

    /**
     * Gets the value of the attribute top.
     *
     * @return The value of the attribute top.
     */
    public IntOrPercent getTop() {
        return top;
    }

    /**
     * Sets the value for the attribute left.
     *
     * @param left The value of the attribute. It can contain a
     *        '%' sign at the end to denote the value in percentage.
     * @exception NumberFormatException The value does not contain a number.
     */
    public void setLeft( String left ) throws NumberFormatException {
        this.left = new IntOrPercent( left );
    }

    /**
     * Gets the value of the attribute left.
     *
     * @return The value of the attribute left.
     */
    public IntOrPercent getLeft() {
        return left;
    }

    /**
     * Sets the value for the attribute height.
     *
     * @param height The value of the attribute. It can contain a
     *        '%' sign at the end to denote the value in percentage.
     * @exception NumberFormatException The value does not contain a number.
     */
    public void setHeight( String height ) throws NumberFormatException {
        this.height = new IntOrPercent( height );
    }

    /**
     * Gets the value of the attribute height.
     *
     * @return The value of the attribute height.
     */
    public IntOrPercent getHeight() {
        return height;
    }

    /**
     * Sets the value for the attribute width.
     *
     * @param width The value of the attribute. It can contain a
     *        '%' sign at the end to denote the value in percentage.
     * @exception NumberFormatException The value does not contain a number.
     */
    public void setWidth( String width ) throws NumberFormatException {
        this.width = new IntOrPercent( width );
    }

    /**
     * Gets the value of the attribute width.
     *
     * @return The value of the attribute width.
     */
    public IntOrPercent getWidth() {
        return width;
    }

    /**
     * Sets the value for the attribute fit.
     *
     * @param fit The value of the attribute as one of the enumerated values in <code>Fit</code>.
     */
    public void setFit( Fit fit ) {
        this.fit = fit;
    }

    /**
     * Sets the value for the attribute fit.
     *
     * @param fit The value of the attribute as <code>String</code>.
     */
    public void setFit( String fit ) {
        this.fit = Fit.valueOf( fit );
    }

    /**
     * Gets the value of the attribute fit.
     *
     * @return The value of the attribute fit.
     */
    public Fit getFit() {
        return fit;
    }

    private IntOrPercent top;
    private IntOrPercent left;
    private IntOrPercent width;
    private IntOrPercent height;
    private Fit fit;

}
