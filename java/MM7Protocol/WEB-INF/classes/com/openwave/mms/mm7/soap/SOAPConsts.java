package com.openwave.mms.mm7.soap;

public class SOAPConsts {

//prefixes
    public static final String SOAPEnvPrefix = "env";
    public static final String SOAPMM7Prefix = "mm7";
    public static final String SOAPEnvelope  = "Envelope";
    public static final String SOAPHeader    = "Header";
    public static final String SOAPBody      = "Body";
    public static final String SOAPFault     = "Fault";
    
//namespaces
    public static final String MM7Namespaces[] = 
        { "http://www.3gpp.org/ftp/Specs/archive/23_series/23.140/schema/REL-5-MM7-1-0",
          "http://www.3gpp.org/ftp/Specs/archive/23_series/23.140/schema/REL-5-MM7-1-1",
          "http://www.3gpp.org/ftp/Specs/archive/23_series/23.140/schema/REL-5-MM7-1-2",
          "http://www.3gpp.org/ftp/Specs/archive/23_series/23.140/schema/REL-5-MM7-1-3" };
    public static final String SOAPNamespace = "http://schemas.xmlsoap.org/soap/envelope/";
    public static final String OPWVNamespace = "http://www.openwave.com/mms/mm7_extensions/1-0";

//method names
    public static final String MM7SubmitReqMethodName = "SubmitReq";
    public static final String MM7SubmitResMethodName = "SubmitRsp";
    public static final String MM7CancelReqMethodName = "CancelReq";
    public static final String MM7CancelResMethodName = "CancelRsp";
    public static final String MM7ReplaceReqMethodName = "ReplaceReq";
    public static final String MM7ReplaceResMethodName = "ReplaceRsp";
    public static final String MM7DeliverReqMethodName = "DeliverReq";
    public static final String MM7DeliverResMethodName = "DeliverRsp";
    public static final String MM7DeliveryReportReqMethodName = "DeliveryReportReq";
    public static final String MM7DeliveryReportResMethodName = "DeliveryReportRsp";
    public static final String MM7ReadReplyReqMethodName = "ReadReplyReq";
    public static final String MM7ReadReplyResMethodName = "ReadReplyRsp";
    public static final String MM7GetDeviceProfileReqMethodName = "GetDeviceProfileReq";
    public static final String MM7GetDeviceProfileResMethodName = "GetDeviceProfileRsp";
    public static final String MM7VASPErrorRspMethodName = "VASPErrorRsp";
    public static final String MM7RSErrorRspMethodName = "RSErrorRsp";

//parameter names
    public static final String MM7TransactionIdParameterName = "TransactionID";
    public static final String MM7MM7VersionParameterName = "MM7Version";
    public static final String MM7VASPIdParameterName = "VASPID";
    public static final String MM7VASIdParameterName = "VASID";
    public static final String MM7RelayServerIdParameterName = "MMSRelayServerID";
    public static final String MM7MessageTypeParameterName = "MessageType";
    public static final String MM7MessageClassParameterName = "MessageClass";
    public static final String MM7MessageIDParameterName = "MessageID";
    public static final String MM7SenderParameterName = "Sender";
    public static final String MM7SenderAddressParameterName = "SenderAddress";
    public static final String MM7RecipientsParameterName = "Recipients";
    public static final String MM7RecipientParameterName = "Recipient";
    public static final String MM7ToParameterName = "To";
    public static final String MM7CcParameterName = "Cc";
    public static final String MM7BccParameterName = "Bcc";
    public static final String MM7NumberParameterName = "Number";
    public static final String MM7EmailParameterName = "RFC2822Address";
    public static final String MM7ShortCodeParameterName = "ShortCode";
    public static final String MM7ServiceCodeParameterName = "ServiceCode";
    public static final String MM7LinkedIDParameterName = "LinkedID";
    public static final String MM7MMSRelayIDParameterName = "MMSRelayServerID";
    public static final String MM7TimeStampParameterName = "TimeStamp";
    public static final String MM7ExpiryDateParameterName = "ExpiryDate";
    public static final String MM7DateParameterName = "Date";
    public static final String MM7ExpiryParameterName = "Expiry";
    public static final String MM7SubjectParameterName = "Subject";
    public static final String MM7EarliestDeliveryTimeParameterName = "EarliestDeliveryTime";
    public static final String MM7DeliveryReportParameterName = "DeliveryReport";
    public static final String MM7ReadReplyParameterName = "ReadReply";
    public static final String MM7ReplyChargingParameterName = "ReplyCharging";
    public static final String MM7ReplyChargingIDParameterName = "ReplyChargingID";
    public static final String MM7PriorityParameterName = "Priority";
    public static final String MM7ChargedPartyParameterName = "ChargedParty";
    public static final String MM7DistributionIndicatorParameterName = "DistributionIndicator";
    public static final String MM7DistributionProtectionParameterName = "DistributionProtection";
    public static final String MM7ContentTypeParameterName = "ContentType";
    public static final String MM7ContentParameterName = "Content";
    public static final String MM7StatusCodeParameterName = "StatusCode";
    public static final String MM7StatusTextParameterName = "StatusText";
    public static final String MM7StatusParameterName = "Status";
    public static final String MM7MMStatusParameterName = "MMStatus";
    public static final String MM7DetailsParameterName = "Details";
    public static final String MM7DetailParameterName = "detail";
    public static final String MM7FaultCodeParameterName = "faultcode";
    public static final String MM7FaultStringParameterName = "faultstring";
    public static final String MM7SenderIdentificationParameterName = "SenderIdentification";
    public static final String MM7VASPIDParameterName = "VASPID";
    public static final String MM7VASIDParameterName = "VASID";
    public static final String MM7TransactionIDParameterName = "TransactionID";
    public static final String MM7UserParameterName = "User";

//parameter values
    public static final String MM7PriorityHighParameterValue = "High";
    public static final String MM7PriorityNormalParameterValue = "Normal";
    public static final String MM7PriorityLowParameterValue = "Low";
    public static final String MM7MMStatusExpiredParameterValue = "Expired";
    public static final String MM7MMStatusRetrievedParameterValue = "Retrieved";
    public static final String MM7MMStatusRejectedParameterValue = "Rejected";
    public static final String MM7MMStatusIndeterminateParameterValue = "Indeterminate";
    public static final String MM7MMStatusForwardedParameterValue = "Forwarded";
    public static final String MM7MM7VersionParameterValue[] = { "5.3.0", "5.5.0", "5.6.0" };
    public static final String MM7StatusOKParameterValue = "1000";
    public static final String MM7ChargedPartySenderParameterValue = "Sender";
    public static final String MM7ChargedPartyRecipientParameterValue = "Recipient";
    public static final String MM7ChargedPartyBothParameterValue = "Both";
    public static final String MM7ChargedPartyNeitherParameterValue = "Neither";
    public static final String MM7InformationalParameterValue = "Informational";
    public static final String MM7AdvertisementParameterValue = "Advertisement";
    public static final String MM7AutoParameterValue = "Auto";
    public static final String MM7PersonalParameterValue = "Personal";
    public static final String MM7ProfileURLParameterValue = "ProfileURL";
    public static final String MM7UserAgentParameterValue = "UserAgent";
    public static final String MM7UserDeviceParameterValue = "UserDevice";

//attribute names
    public static final String MM7ReplyDeadlineAttributeName = "replyDeadline";
    public static final String MM7ReplyChargingSizeAttributeName = "replyChargingSize";
    public static final String MM7ReplyAllowAdaptationAttributeName = "allowAdaptations";
    public static final String MM7ContentHrefAttributeName = "href";

//attribute values

// misc
    public static final String MM7SoapContentType = "text/xml";
    public static final String MM7SWAContentType = "multipart/related";

//header names
    public static final String MM7ContentIDHeaderName = "content-id";

}
