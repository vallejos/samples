// EDITOR NOTE: Please set number of columns to 100 in ur editor.

package com.openwave.mms.content;

import java.util.HashMap;

/**
 * This class defines a typesafe-enum representing the different values
 * of the "fit" attribute of the "region" SMIL element.
 */
public final class Fit {

    /**
     * This private constructor ensures that only values created herein are used.
     *
     * @param fit The fit value.
     */
    private Fit( String fit ) {
        this.fit = fit;
        allowedValues.put( fit.toLowerCase(),
                           this );
    }
   
    /**
     * Returns a <code>Fit</code> object based on the value of the fit
     * <code>String</code> supplied.
     *
     * @param fit The value to convert.
     * @return <code>Fit</code> object that corresponds to the given
     *         <code>String</code> value.
     */
    public static Fit valueOf( String fit ) {
        if( fit == null ) return null;
        return ( Fit ) allowedValues.get( fit.toLowerCase() );
    }
   
    /**
     * Gets the value of the <code>String</code> object.
     *
     * @return The string value of this object.
     */
    public String toString() {
        return fit;
    }

    // This line must be here.
    private static HashMap allowedValues = new HashMap();

    /**
     *  String constant that defines the Fit of a region as hidden.
     */
    public static final Fit HIDDEN = new Fit( "hidden" );

    /**
     *  String constant that defines the Fit of a region as slice.
     */
    public static final Fit SLICE = new Fit( "slice" );

    /**
     *  String constant that defines the Fit of a region as fill.
     */
    public static final Fit FILL = new Fit( "fill" );

    /**
     *  String constant that defines the Fit of a region as meet.
     */
    public static final Fit MEET = new Fit( "meet" );

    /**
     *  String constant that defines the Fit of a region as roll.
     */
    public static final Fit ROLL = new Fit( "roll" );

    private String fit;
}
