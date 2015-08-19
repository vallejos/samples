// EDITOR NOTE: Please set number of columns to 100 in ur editor.

package com.openwave.mms.content;

/**
 * This class denotes the attribute value which can be specified either as
 * an integer or a percentage. This is typically used for expressing
 * the values of a SMIL region's attributes, such as top, left etc.
 */

public final class IntOrPercent {

    /**
     * The constructor takes a <code>String</code> as input and converts it into an
     * <code>IntOrPercent</code> object. If the string passed in has a '%' sign at the
     * end, the type is set to PERCENT, otherwise it is set to INT.
     *
     * @param value The value as a <code>String</code> object. It can contain a '%'
     *        sign at the end.
     * @throws NumberFormatException The integer part of the string does not contain
     *         an integer.
     */
    public IntOrPercent( String value ) throws NumberFormatException {
        if( value == null || value.length() == 0 ) {
            this.value = 0;
            type = INT;
        } else {
            if( value.endsWith( "%" ) ) {
                type = PERCENT;
                value = value.substring( 0, value.length() - 1 );
            } else {
                type = INT;
            }

            this.value = Integer.parseInt( value );
        }
    }

    /**
     * Gets the value part. A client must use the <code>getType</code> method
     * to determine whether this value represents an integer or a percentage.
     *
     * @return The value part of the object.
     */
    public int getValue() {
        return value;
    }

    /**
     * Gets the type of the value in the object. It can be used
     * with the <code>getValue</code> method to determine whether the value
     * returned is an integer or a percentage.
     *
     * @return The type of the value. Can be compared with the static constants
     *         INT and PERCENT defined in this class.
     */
    public int getType() {
        return type;
    }

    /**
     * Gets the string representation of the value.
     */
    public String toString() {
        return ( type == INT ) ? String.valueOf( value ) : String.valueOf( value ) + "%";
    }

    /**
     * Converts the input string into an IntOrPercent object.
     */
    public static IntOrPercent valueOf( String value ) {
        if( value == null ) return null;
        return new IntOrPercent( value );
    }

    /**
     * Static member representing a value of type integer.
     */
    public static final int INT = 0;

    /**
     * Static member representing a value of type percentage.
     */
    public static final int PERCENT = 1;

    private int value;
    private int type;

}
