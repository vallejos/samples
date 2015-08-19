package com.openwave.mms.mm7;

import java.util.Calendar;
import java.util.NoSuchElementException;
import java.lang.NumberFormatException;
import java.util.StringTokenizer;
import java.util.Vector;

import com.openwave.mms.mm7.soap.SOAPConsts;
import com.openwave.mms.mm7.soap.SOAPException;
import com.openwave.mms.mm7.soap.SOAPMethod;
import com.openwave.mms.mm7.soap.SOAPParameter;
import com.openwave.mms.mm7.util.Utils;

/**
 *  This class encapsulates an MM7 DeliveryReport request that the application  
 *  receives from MMSC. 
 *  <p> 
 *  When the application receives a DeliveryReport request, the API creates a 
 *  <code>DeliveryReport</code> object from the SOAP message and passes it
 *  to the <code>processDeliveryReport</code> method of a custom class that
 *  either implements the {@link MessageListener} interface or extends the 
 *  {@link MessageListenerAdapter} class. 
 *  <p>
 *  When processing DeliveryReport requests, follow these guidelines:
 * <OL>
 * <LI> Use the <code>DeliveryReport</code> accessors to retrieve data about the request.
 * <LI> Determine whether to accept or reject the request. If you accept the request, 
 *      create and return a {@link DeliveryReportResponse} object; otherwise
 *      create and return a {@link FaultResponse} object or throw a {@link
 *      MessageProcessingException} to have the API create the fault response for you.
 * </OL>
 * </p>
 * For further information about using this class to process delivery report requests from
 * MMSC, including guidelines on accepting and rejecting delivery report requests,  
 * see the <em>Openwave MMS Library Developer's Guide</em>.
 */ 
public final class DeliveryReport {

// TODO dont know if i should convert this into typesafe enum

    /** Integer constant that identifies the status of a multimedia message
     *  as expired. This value is one of the possible delivery status values returned 
     *  by the {@link #getMMStatus getMMStatus} method.
     */ 
    public static final int MMStatusExpired = 0;

    /** Integer constant that identifies the status of a multimedia message
     *  as retrieved by the subscriber. This value is one of the possible delivery  
     *  status values returned by the {@link #getMMStatus getMMStatus} method.
     */ 
    public static final int MMStatusRetrieved = 1;

    /** Integer constant that identifies the status of a multimedia message
     *  as rejected by the subscriber. This value is one of the possible delivery  
     *  status values returned by the {@link #getMMStatus getMMStatus} method.
     */ 
    public static final int MMStatusRejected = 2;

    /** Integer constant that identifies the status of a multimedia message
     *  as indeterminate. This value is one of the possible delivery  
     *  status values returned by the {@link #getMMStatus getMMStatus} method.
     */ 
    public static final int MMStatusIndeterminate = 3;

    /** Integer constant that identifies the status of a multimedia message,
     *  as forwarded. This value is one of the possible delivery  
     *  status values returned by the {@link #getMMStatus getMMStatus} method.
     */ 
    public static final int MMStatusForwarded = 4;

    /** Integer constant that identifies there is no status of a multimedia
     *   message. This value is one of the possible status values returned
     *  by the {@link #getMMStatus getMMStatus} method.
     */
    public static final int MMStatusNone = 5;

    /**
     *  Package-private constructor for the DeliveryReport. The API creates this object when
     *  it receives a Delivery Report from the Relay. It then calls the processDeliveryReport
     *  method on the MessageListener object previously registered with the API.
     *
     *  @param method The SOAPMethod object from which to construct the DeliveryReport.
     *  @throws SOAPException Thrown when there is an error retrieving values from the
     *          SOAP packet.
     */
    DeliveryReport( SOAPMethod method )
                    throws SOAPException {
        namespace = method.getNamespace();
        messageID = method.getValue( SOAPConsts.MM7MessageIDParameterName );
        SOAPParameter recipient = method.getParameter( SOAPConsts.MM7RecipientParameterName );
        Vector recipients = recipient.getParameters();
        if( recipients != null && recipients.size() > 0 ) {
            // only one recipient is there, if any
            this.recipient = ( ( SOAPParameter ) recipients.get( 0 ) ).getValue();
        }

        SOAPParameter sender = method.getParameter( SOAPConsts.MM7SenderParameterName );
        Vector senders = sender.getParameters();
        if( senders != null && senders.size() > 0 ) {
            // only one sender is there, if any
            this.sender = ( ( SOAPParameter ) senders.get( 0 ) ).getValue();
        }

        date = method.getValue( SOAPConsts.MM7TimeStampParameterName );
        statusText = method.getValue( SOAPConsts.MM7StatusTextParameterName );
        mmStatus = method.getValue( SOAPConsts.MM7MMStatusParameterName );
    }

    /**
     *  Gets the message ID of the message for which this object is the 
     *  corresponding delivery report.
     *
     *  @return The ID of the message for which this delivery report was generated.
     */
    public String getMessageID() { return messageID; }

    /**
     *  Gets the recipient address of the original message for which this object is the corresponding
     *  delivery report.
     *
     *  @return The recipient address of the message for which this delivery report was generated.
     */
    public String getRecipient() { return recipient; }

    /**
     *  Gets the sender address of the original message for which this object is the corresponding
     *  delivery report.
     *
     *  @return The sender address of of the message for which this delivery report was generated.
     */
    public String getSender() { return sender; }

    /**
     *  Gets the time and date at which this delivery report was generated.
     *
     *  @return The time and date at which this delivery request was generated.
     *  @throws APIException If the API cannot convert the date and time from ISO-8601 format.
     */
    public Calendar getDate() throws APIException { 
        Calendar date = Utils.convertDateFromISO8601( this.date );
        if( date == null ) {
            throw new APIException( "error-parsing-date", this.date );
        }
        return date;
    }

    /**
     *  Gets the status of the message previously submitted for delivery for which this is the 
     *  corresponding delivery report. Use the {@link #getStatusText getStatusText} method to 
     *  retrieve the text associated with the status this method returns.
     *
     *  @return The status of the message referenced in this delivery report.
     */
    public int getMMStatus() {
        if( mmStatus == null ) return MMStatusNone;

        if( mmStatus.equalsIgnoreCase( "expired" ) ) {
            return MMStatusExpired;
        } else if( mmStatus.equalsIgnoreCase( "rejected" ) ) {
            return MMStatusRejected;
        } else if( mmStatus.equalsIgnoreCase( "indeterminate" ) ) {
            return MMStatusIndeterminate;
        } else if( mmStatus.equalsIgnoreCase( "forwarded" ) ) {
            return MMStatusForwarded;
        } else if( mmStatus.equalsIgnoreCase( "retrieved" ) ) {
            return MMStatusRetrieved;
        } else return MMStatusNone;
    }

    /**
     *  Gets the text description that corresponds to the status of the original message for which 
     *  this object is the corresponding delivery report. Use this method to retrieve the text 
     *  that corresponds to the status returned by the {@link #getMMStatus getMMStatus} method.  
     *
     *  @return The status text associated with status of the message referenced in this delivery report.
     */
    public String getStatusText() {
        return statusText;
    }

    /**
     * Package-private method used by the RelayConnection class
     * to relay the namespace from request to the response.
     */
    String getNamespace() {
        return namespace;
    }

    private String messageID;
    private String recipient;
    private String sender;
    private String date;
    private String mmStatus;
    private String statusText;
    private String namespace;

}
