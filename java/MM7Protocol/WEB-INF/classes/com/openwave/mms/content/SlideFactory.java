// EDITOR NOTE: Please set number of columns to 100 in ur editor.

package com.openwave.mms.content;

/**
 * This class creates concrete implementation objects for the 
 * <code>Slide</code> interface.
 */

public class SlideFactory {

    /**
     * Private constructor to make sure that no one else can create instances
     * of this class. This class must be a singleton and clients should get
     * instances by calling the static getInstance method.
     */
    private SlideFactory() {
    }

    /**
     * Returns an instance of this class.
     *
     * @return An instance of this class.
     */
    public static SlideFactory getInstance() {
        return factory;
    }

    /**
     * Obtains a reference to a concrete implementation of the
     * <code>Slide</code> interface.
     */
    public Slide newSlide() {
        return new SlideImpl();
    }

    private static SlideFactory factory = new SlideFactory();

}
