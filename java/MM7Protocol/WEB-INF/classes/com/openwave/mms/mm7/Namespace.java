package com.openwave.mms.mm7;

import com.openwave.mms.mm7.util.Enum;
import com.openwave.mms.mm7.soap.SOAPConsts;

/**
 *  This class encapsulates the various versions of the MM7 URI.
 */
public class Namespace extends Enum {
    /**
     *  Private constructor ensures that only values created herein are used.
     *
     *  @param mm7Uri The mm7 uri value.
     */
    private Namespace( String mm7Uri ) {
        super( mm7Uri );
    }

    /**
     *  Returns the Namespace object corresponding to the input.
     *
     *  @return The Namespace as a <code>String</code> object.
     */
    public static Namespace valueOf( String namespaceName ) {
        return ( Namespace ) allowedValues.get( namespaceName.toLowerCase() );
    }

    /**
     *  Constant that identifies the REL-5-MM7-1-0 version.
     */
    public static final Namespace REL_5_MM7_1_0 =
           new Namespace( SOAPConsts.MM7Namespaces[0] );

    /**
     *  Constant that identifies the REL-5-MM7-1-1 version.
     */
    public static final Namespace REL_5_MM7_1_1 =
           new Namespace( SOAPConsts.MM7Namespaces[1] );

    /**
     *  Constant that identifies the REL-5-MM7-1-2 version.
     */
    public static final Namespace REL_5_MM7_1_2 =
           new Namespace( SOAPConsts.MM7Namespaces[2] );

    /**
     *  Constant that identifies the REL-5-MM7-1-3 version.
     */
    public static final Namespace REL_5_MM7_1_3 =
           new Namespace( SOAPConsts.MM7Namespaces[3] );

}
