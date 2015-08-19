package com.openwave.mms.mm7.util;

import java.util.HashMap;

/**
 *  This class represents an C++ style enum.
 */
public class Enum {
    protected Enum( String value ) {
        this.value = value;
        allowedValues.put( value.toLowerCase(),
                           this );
    }

    public String toString() {
        return value;
    }

    private String value; 
    protected static HashMap allowedValues = new HashMap();
}
