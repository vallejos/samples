package com.openwave.mms.mm7;

import javax.mail.internet.AddressException;
import javax.mail.internet.MimeUtility;
import javax.mail.internet.InternetAddress;

import com.openwave.mms.mm7.soap.SOAPConsts;

/**
 *  This class encapsulates information about the recipient of a message.
 */
public final class Recipient {

    /**
     *  This inner class encapsulates the constants that identify
     *  the type of message recipient (To, Cc, Bcc).
     *  The <code>Recipient</code> class uses a <code>Recipient.Type</code> object 
     *  instance in its constructor to indicate the type of recipient. 
     */
    public static class Type {
        /**
         *  Private constructor ensures that only the static constants declared herein
         *  are used.
         *
         *  @param type the type of the Recipient.
         */
        private Type( String type ) {
            this.type = type;
        }

        /**
         *  Returns the recipient type as a <code>String</code> object.
         *
         *  @return The recipient type as a <code>String</code> object.
         */
        public String toString() {
            return type;
        }

        /**
         *  String constant that identifies this recipient appears in the message header 
         *  <code>To</code> field.
         */
        public static final Type TO = new Type( SOAPConsts.MM7ToParameterName );

        /**
         * String constant that identifies this recipient appears in the message header 
         *  <code>Cc</code> field.
         */
        public static final Type CC = new Type( SOAPConsts.MM7CcParameterName );

        /**
         * String constant that identifies this recipient appears in the message header 
         *  <code>Bcc</code> field.
         */
        public static final Type BCC = new Type( SOAPConsts.MM7BccParameterName );

        private String type;
    }

    /**
     *  Instantiates a <code>Recipient</code> object.
     *
     *  @param type Type of recipient. Must be one of values defined by the {@link Recipient.Type} 
     *  class.
     *  @param addressType Type of recipient address. Must be one of values defined by the
     *  {@link AddressType} class.
     *  @param address Address of recipient.
     *  @exception APIException  If the value of <code>recipientType</code> or <code>recipientAddrType</code> is 
     *             not one of the defined constants, or if an error occurs when parsing the value of
     *             <code>recipientAddr</code> when declared as type <code>AddressType.EMAIL</code>.
     */
    public Recipient( Type type,
                      AddressType addressType,
                      String address ) throws APIException {
        if( type == null )
            throw new APIException( "use-proper-recipient-type" );

        if( addressType == null )
            throw new APIException( "use-proper-recipient-address-type" );

        this.type = type;
        this.addressType = addressType;
        try {
            this.address = addressType == AddressType.EMAIL ?
                           new InternetAddress( address ).toString() :
                           address;
        } catch( AddressException ae ) {
            throw new APIException( "improper-email-address", ae.getMessage() );
        }
    }

    /**
     *  Returns the type of this recipient.
     *
     *  @return The type of this recipient as a <code>Recipient.Type</code> object.
     */
    public Type getType() { return type; }

    /**
     *  Returns the address type of this recipient.
     *
     *  @return The address type of this recipient as a <code>AddressType</code> object.
     */
    public AddressType getAddressType() { return addressType; }

    /**
     *  Returns the address of this recipient.
     *
     *  @return The address of this recipient as a <code>String</code> object.
     */
    public String getAddress() { return address; }

    private Type type;
    private AddressType addressType;
    private String address;
}
