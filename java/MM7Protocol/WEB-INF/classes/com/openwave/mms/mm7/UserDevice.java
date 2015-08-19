package com.openwave.mms.mm7;

import javax.mail.internet.AddressException;
import javax.mail.internet.MimeUtility;
import javax.mail.internet.InternetAddress;

import com.openwave.mms.mm7.soap.SOAPConsts;
import com.openwave.mms.mm7.ErrorCode;

/**
 *  This class encapsulates information about the recipient of a message.
 */
public final class UserDevice {

    public static final int USERAGENT = 1;
    public static final int PROFILEURL = 2;

    /**
     *  Instantiates a <code>UserProfile</code> object.
     *
     *  @param phoneNumber Phone number of the user.
     *  @param type        Profile URL or User Agent.  Must be a value defined 
     *  by the {@link Recipient.Type} class.
     *  @param description Profile URL or User Agent string returned from MMSC.
     *  @param statusCode  Return status code of profile query from MMSC.
     *  @exception APIException  The value of <code>description</code> is
     *             not one of the defined constants.
     */
    UserDevice( String phoneNumber,
                int type,
                String description,
                ErrorCode statusCode ) throws APIException {
        if( phoneNumber == null )
            throw new APIException( "need-phone-number-specified" );

        this.phoneNumber = phoneNumber;
        this.type = type;
        this.description = description;
        this.statusCode = statusCode;
    }

    /**
     *  Returns the phone number of the recipient.
     *
     *  @return The phone number of the recipient as a <code>String</code> object.
     */
    public String getPhoneNumber() { return phoneNumber; }

    /**
     *  Returns the type of the user profile.
     *
     *  @return The type of the user profile as a <code>UserProfile.Type</code> object.
     */
    public int getType() { return type; }

    /**
     *  Returns the profile URL/user agent of the user.
     *
     *  @return The profile URL/user agent of the user as a <code>String</code> object.
     */
    public String getDescription() { return description; }

    /**
     *  Returns the status code of the user.
     *
     *  @return The status code of the user as an <code>ErrorCode</code> object.
     */
    public ErrorCode getStatusCode() { return statusCode; }

    /**
     *  Returns the status text of the user.
     *
     *  @return The status text of the user as a <code>String</code> object.
     */
    public String getStatusText() { return statusCode.getName(); }

    private String phoneNumber;
    private int type;
    private String description;
    private ErrorCode statusCode;
}
