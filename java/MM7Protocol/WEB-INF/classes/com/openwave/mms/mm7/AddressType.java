package com.openwave.mms.mm7;

import com.openwave.mms.mm7.soap.SOAPConsts;

/**
 *  This class encapsulates the constants that identify the
 *  type of sender/recipient address. 
 */
public final class AddressType {
    /**
     *  Private constructor ensures that only the static constants declared herein
     *  are used.
     *
     *  @param type the address type of the Recipient.
     */
    private AddressType( String addressType ) {
        this.addressType = addressType;
    }

    /**
     *  Returns the address type of the message recipient as a <code>String</code> object.
     *
     *  @return The address type of the message recipient as a <code>String</code> object.
     */
    public String toString() {
        return addressType;
    }

    /**
     *  String constant that identifies this recipient address is of number type. This type
     *  of address is represented as an E.164 Mobile Station ISDN Number (MSISDN)
     *  address.
     */
    public static final AddressType NUMBER = new AddressType( SOAPConsts.MM7NumberParameterName );

    /**
     *  String constant that identifies this recipient address is of email type. This type
     *  of address is represented as a standard RFC2822 email address.
     */
    public static final AddressType EMAIL = new AddressType( SOAPConsts.MM7EmailParameterName );

    /**
     *  String constant that identifies this recipient address is of short code type. This type
     *  of address is represented as a short series of letters and numbers, as defined by the 
     *  carrier, and acts as an alias to a distribution list.
     */
    public static final AddressType SHORTCODE = new AddressType( SOAPConsts.MM7ShortCodeParameterName );

    private String addressType;
}
