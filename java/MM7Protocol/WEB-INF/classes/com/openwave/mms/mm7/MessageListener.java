package com.openwave.mms.mm7;

/**
 *  This interface defines the methods that applications use to process 
 *  requests from MMSC. It is for use only by applications that operate 
 *  in receive-only or send-and-receive mode. 
 *
 *  You must implement all methods of this interface, even if you do not 
 *  support all request types. To implement methods that correspond to
 *  only those request types you support, extend the 
 *  {@link MessageListenerAdapter} class.
 *  To use this interface:
 *  <OL>
 *  <LI> Create an object that implements this interface. 
 *  <LI> Provide custom implementations of all methods. Because objects that
 *       implement this interface are used by multiple threads, you must 
 *       synchronize reading and writing of data within this object.
 *   </OL>
 * 
 *  For information about how to use your implementation of this interface in
 *  standalone and servlet applications, see the <em>Openwave MMS Library 
 *  Developer's Guide</em>.
 */
public interface MessageListener {
    /**
     *  Processes deliver requests from MMSC. When a deliver request is received
     *  from MMSC, the API automatically calls this method and passes it a 
     *  {@link DeliverRequest} object that represents the request. 
     *  <p>
     *  Use the <code>DeliverRequest</code> accessors to access the contents of 
     *  the request object. After examining the request, if you accept it,
     *  create and return a {@link DeliverResponse} object. If you do not 
     *  accept it, either:
     *  <UL><LI> Create and return a {@link FaultResponse} object that 
     *       indicates the reason you rejected the request.
     *  <LI> Throw a {@link MessageProcessingException} to have the 
     *       API automatically create a <code>FaultResponse</code> object.  
     * </UL></p>
     *
     *  @param deliverRequest The API-generated <code>DeliverRequest</code> object 
     *          that corresponds to the deliver request received from MMSC.
     *  @return The application-generated <code>DeliverResponse</code> or 
     *          <code>FaultResponse</code> object to return to MMSC.
     *  @exception MessageProcessingException If the application cannot
     *             process the message, this exception is thrown to have the API
     *             create the <code>FaultResponse</code> object to return to MMSC.
     *
     */
    public Response processDeliverRequest( DeliverRequest deliverRequest )
                                           throws MessageProcessingException;

    /**
     *  Processes delivery report requests from MMSC. When a delivery report request
     *  is received from MMSC, the API automatically calls this method and passes it a 
     *  {@link DeliveryReport} object that represents the request. 
     *  <p>
     *  Use the <code>DeliveryReport</code> accessors to access the contents of 
     *  the request object. After examining the request, if you accept it,
     *  create and return a {@link DeliveryReportResponse} object. If you do not 
     *  accept it, either:
     *  <UL><LI> Create and return a {@link FaultResponse} object that 
     *       indicates the reason you rejected the request.
     *  <LI> Throw a {@link MessageProcessingException} to have the 
     *       API automatically create a <code>FaultResponse</code> object.  
     * </UL></p>
     *
     *  @param deliveryReport The API-generated <code>DeliveryReport</code> object 
     *          that corresponds to the delivery report request received from MMSC.
     *  @return The application-generated <code>DeliveryReportResponse</code> or 
     *          <code>FaultResponse</code> object to return to MMSC.
     *  @exception MessageProcessingException If the application cannot
     *             process the message, this exception is thrown to have the API
     *             create the <code>FaultResponse</code> object to return to MMSC.
     *
     */
    public Response processDeliveryReport( DeliveryReport deliveryReport )
                                           throws MessageProcessingException;

    /**
     *  Processes read-reply requests from MMSC. When a read-reply request
     *  is received from MMSC, the API automatically calls this method and  
     *  passes it a {@link ReadReply} object that represents the request. 
     *  <p>
     *  Use the <code>ReadReply</code> accessors to access the contents of 
     *  the request object. After examining the request, if you accept it,
     *  create and return a {@link ReadReplyResponse} object. If you do not 
     *  accept it, either:
     *  <UL><LI> Create and return a {@link FaultResponse} object that 
     *       indicates the reason you rejected the request.
     *  <LI> Throw a {@link MessageProcessingException} to have the 
     *       API automatically create a <code>FaultResponse</code> object.  
     * </UL></p>
     *
     *  @param readReply The API-generated <code>ReadReply</code> object 
     *          that corresponds to the read-reply request received from MMSC.
     *  @return The application-generated <code>ReadReplyResponse</code> or 
     *          <code>FaultResponse</code> object to return to MMSC.
     *  @exception MessageProcessingException If the application cannot
     *             process the message, this exception is thrown to have the API
     *             create the <code>FaultResponse</code> object to return to MMSC.
     *
     */
    public Response processReadReply( ReadReply readReply )
                                      throws MessageProcessingException;
}
