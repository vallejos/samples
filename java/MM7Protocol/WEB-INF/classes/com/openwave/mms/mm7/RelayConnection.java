package com.openwave.mms.mm7;

import java.io.BufferedOutputStream;
import java.io.ByteArrayOutputStream;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.util.HashMap;
import java.net.HttpURLConnection;
import java.io.IOException;
import java.io.InputStream;
import java.security.MessageDigest;
import java.net.MalformedURLException;
import java.security.NoSuchAlgorithmException;
import java.io.OutputStream;
import java.io.PrintWriter;
import java.util.Properties;
import java.util.StringTokenizer;
import java.net.Socket;
import java.net.URL;
import java.net.UnknownHostException;
import java.util.Vector;

import javax.security.cert.CertificateExpiredException;
import javax.security.cert.CertificateNotYetValidException;
import javax.mail.internet.InternetHeaders;
import javax.mail.MessagingException;
import javax.mail.internet.MimeMultipart;
import javax.net.ssl.SSLSocket;
import javax.net.ssl.SSLSocketFactory;
import javax.security.cert.X509Certificate;
import javax.servlet.ServletRequest;
import javax.servlet.ServletResponse;

import org.apache.log4j.Logger;
import org.apache.log4j.Level;

import com.globalnet.standalone.SimpleSender;
import com.openwave.mms.mm7.util.Base64;
import com.openwave.mms.mm7.util.InputStreamDataSource;
import com.openwave.mms.mm7.util.RecordedInputStream;
import com.openwave.mms.mm7.soap.SOAPConsts;
import com.openwave.mms.mm7.soap.SOAPException;
import com.openwave.mms.mm7.soap.SOAPMethod;
import com.openwave.mms.mm7.soap.SOAPParser;

import com.openwave.mms.content.ContentException;

/**
 *  This class encapsulates the connection that applications use to
 *  send MM7 messages to and receive them from MMSC. Applications use
 *  <code>RelayConnection</code> objects to establish connections with
 *  MMSC in one of three modes:
 *  <UL>
 *  <LI> Send-only,
 *  <LI> Receive-only,
 *  <LI> Send and Receive,
 *  </UL>
 *  <p>
 *  Use this class to handle the application connection with MMSC by following these 
 *  guidelines: 
 *  <OL>
 *  <LI> Create a <code>RelayConnection</code> object.
 *  <LI> Initiate a connection with MMSC in one of the three operating modes. 
 *
 *  <LI> For standalone applications in receive-only or send and receive mode, 
 *       specify the type of HTTP Access Authentication supported. 
 *
 *  <LI> If using the Secure Socket Layer (SSL) protocol, specify whether your
 *       server certificate has a weak common name using the {@link #setWeakCN
 *       setWeakCN} method.
 *
 *  <LI> For standalone applications in receive-only or send and receive mode,
 *       create an object that implements the {@link MessageListener} interface 
 *       or extends the {@link MessageListenerAdapter} class and register 
 *       the object with the <code>RelayConnection</code> object using
 *       the {@link #setMessageListener setMessageListener} method.
 * 
 *  <LI> To send messages to MMSC from applications operating in send-only or 
 *       send and receive mode, create a {@link SubmitRequest} object that 
 *       contains the message and use the connection's {@link #sendRequest 
 *       sendRequest} method to send it. The API encapsulates the response from MMSC 
 *       in a {@link SubmitResponse} or {@link FaultResponse} object 
 *       and returns it.
 *
 *  <LI> When the API receives requests from MMSC, it converts the request to
 *       a {@link DeliverRequest}, {@link DeliveryReport}, or {@link ReadReply}
 *       object. For standalone applications, the API passes the request object 
 *       to the custom message listener object that was registered with the 
 *       <code>RelayConnection</code> object. For servlet
 *       applications, use the connection's {@link #dispatch dispatch} method  
 *       in the servlet's <code>doPost</code> method to dispatch the request to a
 *       MessageListener object.
 *       To return the appropriate response, create a response object and use it as
 *       the return value of the message listener method that processes the request.
 *       The <code>RelayConnection</code> object converts the response to SOAP format
 *       and returns it to MMSC.   
 * </OL>
 * <p>
 * For further information about using this class handle connections between your
 * application and MMSC, see the <em>Openwave MMS Library Developer's Guide</em>.
 *
 *  @version 3.0
 */
public final class RelayConnection {


    /**
     *  This inner class encapsulates the constants that identify
     *  the type of HTTP authentication the API uses to authenticate incoming requests.
     */
    public static class AuthenticationType {
        /**
         *  Private constructor so that AuthenticationTypes are only ones created below.
         *
         *  @param authType The authetication type.
         */
        private AuthenticationType( int authType ) {
            this.authType = authType;
        }

        /**
         *  Static constant that identifies the type of HTTP authentication as 
         *  HTTP Basic Access Authentication.
         */
        public static final AuthenticationType BASIC = new AuthenticationType( 0 );
    
        /**
         *  Static constant that identifies the type of HTTP authentication as 
         *  HTTP Digest Access Authentication.
         */
        public static final AuthenticationType DIGEST = new AuthenticationType( 1 );
    
        /**
         *  Static constant that identifies the type of HTTP authentication as 
         *  either HTTP Basic or Digest Access Authentication.
         */
        public static final AuthenticationType ANY = new AuthenticationType( 2 );
    
        private int authType;
    }

    /**
     *   Creates a connection for communication with MMSC in receiver-only mode.
     *   Applications using this type of connection can only receive requests from
     *   and cannot send them to MMSC. This method should be used only in standalone 
     *   applications. 
     *
     *   @param port The port on which the application listens for connections 
     *               from MMSC.
     *   @param secure A boolean that specifies whether the communication is 
     *               conducted using the Secure Socket Layer (SSL) protocol.
     *               <code>true</code> specifies that the application uses SSL,
     *               <code>false</code> indicates it does not use SSL.
     *   @exception APIException If the API cannot create the server socket.
     */
    public static RelayConnection createReceiver(int port, boolean secure) throws APIException {
        return new RelayConnection(port, secure);
    }

    /**
     *  Creates a connection for communication with MMSC in send-only mode.
     *  Applications using this type of connection can only send requests to
     *  and cannot receive them from MMSC.
     *
     *   @param url The URL on which MMSC listens for requests and to which
     *              the API sends requests.
     *              If the protocol portion of the URL is <code>https</code>, 
     *              the API initiates an SSL connection.
     *   @param userName The numerical portion of the VASP's PLMN address that
     *              the application uses to connect to MMSC. For example,
     *              15551234567.

     *   @param password The password associated with <code>userName</code>
     *              that the application uses to connect to MMSC.
     *   @exception MalformedURLException If the API encounters an error parsing
     *              the <code>url</code>.
     *   @exception APIException If the application does not supply a value for 
     *              <code>userName</code> or <code>password</code>.
     */
    public static RelayConnection createSender(String url, String userName, String password) throws MalformedURLException, APIException {
        return new RelayConnection( url, userName, password );
    }

    /**
     *  Creates a connection for communication with MMSC in send and receive mode.
     *  Applications using this type of connection can send requests to
     *  and receive them from MMSC. This method is valid only for standalone 
     *  applications. 
     *
     *   @param url The URL on which MMSC listens for requests and to which
     *              the API sends requests.
     *              If the protocol portion of the URL is <code>https</code>, 
     *              the API initiates an SSL connection.
     *   @param userName The numerical portion of the VASP's PLMN address that
     *              the application uses to connect to MMSC. For example,
     *              15551234567.

     *   @param password The password that the application uses to connect 
     *              to MMSC.
     *   @param port The port on which the application listens for connections 
     *               from MMSC.
     *   @param secure A boolean that specifies whether the communication is 
     *              conducted using the Secure Socket Layer (SSL) protocol.
     *              <code>true</code> specifies that the application uses SSL,
     *              <code>false</code> indicates it does not use SSL.
     *   @exception MalformedURLException If the API encounters an error parsing
     *              the <code>url</code>.
     *   @exception APIException If the application does not supply a value for 
     *              <code>userName</code> or <code>password</code>.
     */
    public static RelayConnection createSenderAndReceiver(
                             String url,
                             String userName,
                             String password,
                             int port,
                             boolean secure ) throws MalformedURLException,
                                                     APIException {
        return new RelayConnection( url,
                                    userName,
                                    password,
                                    port,
                                    secure ); 
    }

    /**
     *   Sends a <code>Request</code> object that contains a multimedia message
     *   to MMSC for delivery to mobile subscribers and returns a
     *   <code>Response</code> object that encapsulates the MMSC response. 
     *
     *   @param request A {@link SubmitRequest} object.
     *   @return A {@link SubmitResponse} object that contains the MMSC response
     *           to the <code>request</code>.
     *   @exception APIException If the connection to MMSC is not successful,
     *           an error occurs writing the request to the socket, the request 
     *           contains an invalid content type or error in the content type 
     *           header, or an error occurs reading the response from the socket.  
     */
    public Response sendRequest(Request request) throws APIException, ContentException {
        threadCheck();

        if (mode == RECEIVER_ONLY)
            throw new APIException("connection-mode-is-receiver-only");

        Socket socket = createSocket();
        Socket newSocket = null;
        boolean useNewSocket = false;
        
        try {
            int retCode = writeMessage(request, socket, null);
            if (retCode == HttpURLConnection.HTTP_UNAUTHORIZED) {
                WWWAuthenticateHeader wwwAuthHeader = getWWWAuthHeader( socket.getInputStream() );
                String authHeaderValue = computeAuthHeaderValue( wwwAuthHeader );
                socket.close();
                newSocket = createSocket();
                retCode = writeMessage( request, newSocket, authHeaderValue );
                useNewSocket = true;
            }
            
            if (retCode != HttpURLConnection.HTTP_OK)
                throw new APIException("http-return-code", new Integer(retCode));
            
        }catch(IOException ioe) {
            try {
                if (useNewSocket)
                    newSocket.close();
                else
                    socket.close();
            }catch(IOException ioe1) {
                // cant do much
            }
            throw new APIException("io-exception-writing-to-socket", ioe.getMessage());
        }

        try {
            InputStream inStream = useNewSocket ? newSocket.getInputStream() : socket.getInputStream();

            if( logger.isDebugEnabled() ) {
                RecordedInputStream recorder = new RecordedInputStream( inStream );
                logger.debug( "[Begin Incoming Response from Relay]" );
                logger.debug( recorder.getBuffer() );
                logger.debug( "[End Incoming Response from Relay]" );
                inStream = recorder;
            }

            InternetHeaders headers = new InternetHeaders(inStream);
            String[] contentTypes = headers.getHeader("content-type");

            if (contentTypes == null || contentTypes.length == 0)
                throw new APIException("no-content-type-header");

            if (! contentTypes[0].startsWith("text/xml"))
                throw new APIException("unexpected-content-type", contentTypes[0]);

            return ResponseFactory.makeResponse(inStream);

        }catch(IOException ioe) {
            throw new APIException("io-exception-reading-from-socket", ioe.getMessage());
            
        }catch(MessagingException me) {
            throw new APIException("cannot-read-headers", me.getMessage());

        }catch(SOAPException se) {
            throw new APIException(ErrorCode.SERVER_ERROR, se.getMessage());

        }finally{
            try {
                if (useNewSocket)
                    newSocket.close();
                else
                    socket.close();
                
            }catch(IOException ioe) {
                // cant do much
            }
        }
    }

    /**
     *   Sends a <code>Request</code> object that contains a multimedia message
     *   to MMSC for delivery to mobile subscribers and returns a
     *   <code>Response</code> object that encapsulates the MMSC response. 
     *
     *   @param request A {@link SubmitRequest} object.
     *   @return A {@link SubmitResponse} object that contains the MMSC response
     *           to the <code>request</code>.
     *   @exception APIException If the connection to MMSC is not successful,
     *           an error occurs writing the request to the socket, the request 
     *           contains an invalid content type or error in the content type 
     *           header, or an error occurs reading the response from the socket.  
     */
    public void printRequest(Request request, String authHeaderValue) throws APIException, ContentException {
        try {
        	ByteArrayOutputStream outStr = new ByteArrayOutputStream();//socket.getOutputStream();
            outStr.write(("POST " + url.getPath() + " HTTP/1.0\r\n").getBytes());

            if (this.ip != null){
            	outStr.write(("Host: " + this.ip + " \r\n").getBytes());
            }
            
            outStr.write(("Connection: keep-alive\r\n").getBytes());
            outStr.write(("SOAPAction: \"\"\r\n").getBytes());

            if (url.getProtocol().equals("https")) {
                authHeaderValue = "Basic " + Base64.encodeString( userName + ":" + password );
                outStr.write(("Authorization: " + authHeaderValue + "\r\n").getBytes());

            }

            request.writeTo(outStr);
            outStr.flush();
            
            System.out.println(outStr.toString());

        } catch( IOException ioe ) {
            throw new APIException("io-exception-writing-to-socket", ioe.getMessage());
        } catch( SOAPException se ) {
            throw new APIException( se.getMessage() );
        }
    }

    /**
     *   Sets a message listener in the connection object that processes incoming
     *   requests from MMSC. Use this method to set a custom object that implements
     *   the {@link MessageListener} interface or extends the {@link MessageListenerAdapter}
     *   class as the listener that processes incoming requests using custom functionality.
     *
     *   @param messageListener The custom object that implements the <code>
     *          MessageListener</code> interface or extends the 
     *          <code>MessageListenerAdapter</code> class.
     *   @exception APIException If the appliation attempts to set a message listener in
     *          a <code>RelayConnection</code> object that was created in send-only mode.
     */
    public void setMessageListener( MessageListener messageListener )
                                    throws APIException {
        threadCheck();

        if( mode == SENDER_ONLY )
            throw new APIException( "connection-mode-is-sender-only" );

        this.messageListener = messageListener;

        server.setAuthenticators( authenticators );
        server.setAuthType( authType );
        server.startListener();
    }

    MessageListener getMessageListener() { return messageListener; }

    /**
     *  When the API is used in a servlet-based application, this method is used by the
     *  servlet to dispatch an incoming request to a <code>MessageListener</code>. This  
     *  method is valid only in servlet applications.
     *
     *  @param request The servlet request object that encapsulates the incoming request.
     *  @param response The servlet response object that encapsulates the outgoing response.
     *  @param messageListener The custom object that implements the <code>
     *          MessageListener</code> interface or extends the <code>MessageListenerAdapter
     *          </code> class to which the API dispatches the request.
     *  @exception APIException  If the API encounters an error writing the request to 
     *             the socket.
     */
    public static void dispatch( ServletRequest request,
                                 ServletResponse response,
                                 MessageListener messageListener ) throws APIException {
        InputStream inputStream = null;
        OutputStream outputStream = null;
        try {
            inputStream = request.getInputStream();
            
            try {
                Response resp = dispatchInternal(inputStream, request.getContentType(), messageListener);
                writeServletResponse(resp, response);
                
            } catch( APIException ae ) {
                if (logger.isEnabledFor(Level.WARN)) {
                    logger.warn(ae.getLocalizedMessage(), ae);
                }
                FaultResponse res = new FaultResponse(ae.getMessage(), ae.getErrorCode());
                writeServletResponse( res, response );
                throw ae;
            } catch( SOAPException se ) {
                if( logger.isEnabledFor( Level.WARN ) ) {
                    logger.warn( se.getMessage(), se );
                }
                FaultResponse res = new FaultResponse( se.getMessage(),
                                                       ErrorCode.CLIENT_ERROR );
                writeServletResponse( res, response );
                throw new APIException( ErrorCode.CLIENT_ERROR,
                                        se.getMessage() );
            } catch( MessagingException me ) {
                if( logger.isEnabledFor( Level.WARN ) ) {
                    logger.warn( me.getMessage(), me );
                }
                FaultResponse res = new FaultResponse( me.getMessage(),
                                                       ErrorCode.CLIENT_ERROR );
                writeServletResponse( res, response );
                throw new APIException( ErrorCode.CLIENT_ERROR,
                                        me.getMessage() );
            } catch( ContentException ce ) {
                if( logger.isEnabledFor( Level.WARN ) ) {
                    logger.warn( ce.getMessage(), ce );
                }
                FaultResponse res = new FaultResponse( ce.getMessage(),
                                                       ErrorCode.CLIENT_ERROR );
                writeServletResponse( res, response );
                throw new APIException( ErrorCode.CLIENT_ERROR,
                                        ce.getMessage() );
            } catch( MessageProcessingException mpe ) {
                if( logger.isEnabledFor( Level.WARN ) ) {
                    logger.warn( mpe.getMessage(), mpe );
                }
                // write dummy response
                FaultResponse res = new FaultResponse("Client",
                                                      "Client Error",
                                                      mpe.getMessage(),
                                                      mpe.getErrorCode());
                writeServletResponse(res, response);
                
                throw new APIException(ErrorCode.CLIENT_ERROR, mpe.getMessage());
            }
            
        } catch( IOException ioe ) {
            if( logger.isEnabledFor( Level.WARN ) ) {
                logger.warn( ioe.getMessage(), ioe );
            }
            throw new APIException( ErrorCode.SERVER_ERROR,
                                    "io-exception-writing-to-socket",
                                    ioe.getMessage() );
        } catch( SOAPException se ) {
            if( logger.isEnabledFor( Level.WARN ) ) {
                logger.warn( se.getMessage(), se );
            }
            throw new APIException( ErrorCode.SERVER_ERROR,
                                    se.getMessage() );
        }
    }


    /**
     *  Sets the <code>Vector</code> of <code>PasswordAuthentication</code> objects that
     *  contain the user name and password pairs that the API uses to authenticate incoming
     *  requests. This method is valid only for standalone applications operating in
     *  receive-only or send and receive mode. Connection authentication for servlet 
     *  applications is handled by the web server in which the servlet is deployed.
     *
     *  @param authenticators <code>Vector</code> of <code>PasswordAuthentication</code>
     *         objects the API uses to authenticate connection requests.
     *  @exception ClassCastException If the <code>Vector</code> objects are not
     *             of type <code>PasswordAuthentication</code>.
     *  @exception APIException If the current thread does not own this
     *             <code>RelayConnection</code> object.
     *
     */
    public void setAuthenticators( Vector authenticators )
                                   throws ClassCastException, APIException {
        threadCheck();

        this.authenticators = new HashMap();
        if( authenticators != null ) {
            for( int i = 0; i < authenticators.size(); i++ ) {
                java.net.PasswordAuthentication auth = (java.net.PasswordAuthentication) authenticators.get( i );
                this.authenticators.put( auth.getUserName(), auth );
            }
        }
    }

    /**
     *  Sets the type of HTTP authentication used to validate incoming requests. This method
     *  is valid only for standalone applications operating in receive-only or send and receive
     *  mode.
     *
     *  @param authType The type of HTTP authentication to use. Must be an  
     *         {@link AuthenticationType} object.
     *  @exception APIException If the current thread does not own this
     *         <code>RelayConnection</code> object.
     *
     */
    public void setAuthType( AuthenticationType authType ) throws APIException {
        threadCheck();

        if( mode == SENDER_ONLY )
            throw new APIException( "connection-mode-is-sender-only" );

        this.authType = authType;
    }

    /**
     *  When using SSL, indicates to the API whether you are using a server certificate 
     *  that has a weak common name.
     *  A weak common name is a host name used in a test certificate on the server
     *  that may differ from the actual host name where the servier is running. These certificates
     *  are used for testing purposes while waiting to a Certificate Authority to assign a 
     *  certificate.
     *
     *  @param weakCN A boolean that specifies whether the server certificate has a weak 
     *         common name. Specifiy <code>true</code> if certficate has a weak common name;
     *         otherwise specify <code>false</code>, which is the default.
     *  @exception APIException If the current thread does not own this
     *             <code>RelayConnection</code> object.
     *
     */
    public void setWeakCN( boolean weakCN )
                           throws APIException {
        threadCheck();

        this.weakCN = weakCN;
    }

    /**
     *   Constructor for initiatng a receiver-only mode of connection.
     *
     *   @param port The port on which the server will listen for connections.
     *   @param secure True for initiating an SSL server.
     *   @exception APIException If the API cannot create the server socket.
     *
     */
    private RelayConnection( int port,
                             boolean secure ) throws APIException {
        mode = RECEIVER_ONLY;
        this.port = port;

        server = new Server( this, port, secure );

        owningThread = Thread.currentThread();
    }

    /**
     *   Constructor for initiatng a sender-only mode of 
     *   connection.
     *
     *   @param url The URL at which an MMS Relay is available.
     *              If the protocol portion of the URL is https, an SSL
     *              connection will be initiated.
     *   @param userName The user name to be used to connect to the URL.
     *   @param password The password to be used to connect to the URL.
     *   @exception MalformedURLException  if url is not parseable.
     *   @exception APIException  if username/password is not supplied.
     *
     */
    private RelayConnection(String url, String userName, String password) throws MalformedURLException, APIException {
        if( userName == null || userName.length() == 0 )
        	/*
        	 * No exigo password porque algunos MMSC no usan password
        	 *  
        	 * || password == null || password.length() == 0 ) 
        	 */
        	
            throw new APIException( "no-username-password" );

    	try {
        	Properties props = new Properties();
			props.load(RelayConnection.class.getResourceAsStream("/resources/SimpleSender.properties"));
	    	this.ip = props.getProperty("host_ip");
			
		}catch(IOException e){
			throw new APIException("IOException reading properties file: " + e.getMessage());
		}
		
        this.url = new URL( url );
        this.userName = userName;
        this.password = password;
        mode = SENDER_ONLY;
        owningThread = Thread.currentThread();
    }

    /**
     *   Constructor for initiatng a receiver and sender mode of 
     *   connection.
     *
     *   @param url The URL at which an  MMS Relay is available.
     *              If the protocol portion of the URL is https, an SSL
     *              connection will be initiated.
     *   @param userName The user name to be used to connect to the URL.
     *   @param password The password to be used to connect to the URL.
     *   @param port The port on which the receiver will listen for connections.
     *   @param secure True for making the receiver listen for SSL connections only.
     *   @exception MalformedURLException  if Url is not parseable.
     *   @exception APIException  if username/password is not supplied.
     *
     */
    private RelayConnection(String url, String userName, String password, int port, boolean secure) throws MalformedURLException, APIException {

    	if (userName == null || userName.length() == 0)
            throw new APIException( "no-username-password" );
    	
    	try {
        	Properties props = new Properties();
			props.load(RelayConnection.class.getResourceAsStream("/resources/SimpleSender.properties"));
	    	this.ip = props.getProperty("host_ip");
			
		}catch(IOException e){
			throw new APIException("IOException reading properties file: " + e.getMessage());
		}
    	
        this.url = new URL(url);
        this.userName = userName;
        this.password = password;
        this.port = port;
        mode = SENDER_AND_RECEIVER;

        server = new Server(this, port, secure);

        owningThread = Thread.currentThread();
    }

    /**
     *  Package-private method to dispatch the request based on content type.
     *
     *  @param in the input stream to read from.
     *  @param out the output stream to write to.
     *  @param contentType the content type of the request.
     *  @exception APIException if the content type is unrecognized.
     *  @exception IOException generated by stream operations.
     *
     */
    synchronized static Response dispatchInternal(InputStream in, String contentType, MessageListener messageListener) throws IOException, SOAPException, MessageProcessingException, MessagingException, ContentException, APIException {

    	if (contentType == null || contentType.length() == 0)
            throw new APIException("no-content-type-header");

        Response response = null;
        if (contentType.startsWith("text/xml")) {
            // message is DeliveryReport or ReadReply
            SOAPParser parser = new SOAPParser(in);
            String transactionID = parser.getEnvelope().getHeader().getValue(SOAPConsts.MM7TransactionIDParameterName);
            SOAPMethod method = parser.getEnvelope().getBody().getMethod();
            
            if (method.getName().equals(SOAPConsts.MM7DeliveryReportReqMethodName)) {
                DeliveryReport dr = new DeliveryReport(method);
                response = messageListener.processDeliveryReport(dr);
                if (response != null) {
                    // check if response is one of DeliveryReportResponse/FaultResponse
                    if (response.getClass() != DeliveryReportResponse.class &&
                        response.getClass() != FaultResponse.class)
                        throw new APIException(ErrorCode.SERVER_ERROR, "incorrect-delivery-report-response-class");
                    response.setTransactionID(transactionID);
                    response.setNamespace(dr.getNamespace());
                }
                
            }else if(method.getName().equals(SOAPConsts.MM7ReadReplyReqMethodName)) {
                ReadReply rr = new ReadReply(method);
                response = messageListener.processReadReply(rr);
                if (response != null){
                    // check if response is one of ReadReplyResponse/FaultResponse
                    if (response.getClass() != ReadReplyResponse.class &&
                        response.getClass() != FaultResponse.class)
                        throw new APIException(ErrorCode.SERVER_ERROR, "incorrect-read-reply-response-class");
                    response.setTransactionID(transactionID);
                    response.setNamespace(rr.getNamespace());
                }
                
            }else{
                throw new APIException(ErrorCode.UNSUPPORTED_OPERATION, "unknown-mm7-method", method.getName());
            }

        }else if (contentType.startsWith("multipart/related")) {
            InputStreamDataSource ds = new InputStreamDataSource(in, contentType);
            MimeMultipart multipart = new MimeMultipart( ds );
/*
OutputStream out = new FileOutputStream("/var/www/tmp/dibujito.log");
multipart.writeTo(out);
out.close();
*/
            DeliverRequest dr = new DeliverRequest(multipart);
            response = messageListener.processDeliverRequest(dr);
            if (response != null ) {
                // check if response is one of DeliverResponse/FaultResponse
                if (response.getClass() != DeliverResponse.class &&
                    response.getClass() != FaultResponse.class)
                    throw new APIException( ErrorCode.SERVER_ERROR, "incorrect-deliver-response-class");
                response.setTransactionID(dr.getTransactionID());
                response.setNamespace(dr.getNamespace());
            }
            
        }else{
            throw new APIException("unexpected-content-type", contentType);
        }

        return response;
    }

    /**
     *  This method is used to create a socket. If the socket is required to be secure,
     *  it creates an SSL socket and does SSL handshake which included key exchange
     *  and server certificate verification.
     *
     *  @return the socket created.
     *  @exception APIException if there is an io error or if the host is unknown or
     *          if there is an error validating the server certificate.
     */
    private Socket createSocket() throws APIException {
        try {

        	if (url.getProtocol().equals("http")) {
                Socket socket = new Socket(url.getHost(), url.getPort());
                socket.setSoTimeout(2 * 60 * 1000);
                return socket;
                
            }else if (url.getProtocol().equals("https")) {
                SSLSocketFactory factory = (SSLSocketFactory) SSLSocketFactory.getDefault();
                SSLSocket sslSocket = (SSLSocket) factory.createSocket(url.getHost(), url.getPort());
                
                sslSocket.setUseClientMode(true);
                String[] cipherSuites = {"SSL_RSA_WITH_3DES_EDE_CBC_SHA"};
                sslSocket.setEnabledCipherSuites(cipherSuites);
                
                sslSocket.startHandshake();
                X509Certificate[] certs = sslSocket.getSession().getPeerCertificateChain();
                X509Certificate peerCertificate = certs[0];
                peerCertificate.checkValidity();
                String peerName = peerCertificate.getSubjectDN().getName();
                String peerCommonName = getCommonName( peerName );

                if (!weakCN) {
                    if (! peerCommonName.equals(url.getHost())) {
                        throw new APIException("cn-mismatch", "[" + peerCommonName + " != " + url.getHost() + "]");
                    }
                }else{
                    if (! subDomainMatch( peerCommonName, url.getHost())) {
                        throw new APIException("cn-mismatch", "[" + peerCommonName + " != " + url.getHost() + "]");
                    }
                }

                return sslSocket;

            }else throw new APIException("unknown-protocol", url.getProtocol());

        }catch(UnknownHostException uhe) {
            throw new APIException("cannot-create-socket", url.getHost() + ":" + url.getPort());
            
        }catch(IOException ioe) {
            throw new APIException("cannot-create-socket", url.getHost() + ":" + url.getPort());

        }catch(CertificateExpiredException cee) {
            throw new APIException( ErrorCode.SERVER_ERROR, "certificate-expired", url.getHost() + ":" + url.getPort());
            
        }catch(CertificateNotYetValidException cnyv ) {
            throw new APIException(ErrorCode.SERVER_ERROR, "certificate-not-yet-valid", url.getHost() + ":" + url.getPort());
        }
    }

    /**
     *  This method is used to retrieve the common name of the peer
     *
     *  @param peerName the peer name from the certificate.
     *  @throws APIException if common name cannot be found.
     *  @return the peer common name from the peer name.
     *
     */
    private static String getCommonName( String peerName )
                                         throws APIException {
        if( peerName == null ) return null;
        int index = peerName.indexOf( "CN=" );

        if( index != -1 ) {
            index += 3;
            int commaIndex = peerName.indexOf( ',', index );
            if( commaIndex != -1 ) {
                return peerName.substring( index, commaIndex );
            }
        }

        throw new APIException( ErrorCode.SERVER_ERROR,
                                "no-cn-in-cert" );
    }

    /**
     *  This method is used to check if the last two elements of the domain name
     *  of the host we are trying to connect to and the peer common name from the
     *  certificate match.
     *
     *  @param peerCommonName the peer common name from the certificate.
     *  @param hostName the name of the host we are trying to connect to.
     *  @return true if they match, false otherwise.
     *
     */
    private static boolean subDomainMatch( String peerCommonName,
                                           String hostName ) {
        String peerSubDomain = null;
        String hostSubDomain = null;
        int lastDot = peerCommonName.lastIndexOf( '.' );

        if( lastDot != -1 ) {
            int lastButOneDot = peerCommonName.lastIndexOf( '.', lastDot - 1 );
            if( lastButOneDot != -1 ) {
                peerSubDomain = peerCommonName.substring( lastButOneDot + 1 );
            }
        }

        lastDot = hostName.lastIndexOf( '.' );

        if( lastDot != -1 ) {
            int lastButOneDot = hostName.lastIndexOf( '.', lastDot - 1 );
            if( lastButOneDot != -1 ) {
                hostSubDomain = hostName.substring( lastButOneDot + 1 );
            }
        }

        return ( peerSubDomain != null && hostSubDomain != null &&
                 peerSubDomain.equals( hostSubDomain ) );
    }

    /**
     *  This method writes the request to the socket using the authHeaderValue as the value
     *  for the HTTP Authorization header.
     *
     *  @param request the request object that needs to be written.
     *  @param socket the socket to write to.
     *  @param authHeaderValue the value to be used for Authorization header.
     *  @return the HTTP response code.
     *  @exception APIException if there is an IO error, SOAP exception creating the soap packet
     *          from the request object or if there is no response from server.
     *
     */
    private int writeMessage(Request request, Socket socket, String authHeaderValue) throws APIException, ContentException {
        try {
            OutputStream outStr = socket.getOutputStream();
            outStr.write(("POST " + url.getPath() + " HTTP/1.0\r\n").getBytes());

            if (this.ip != null){
            	outStr.write(("Host: " + this.ip + " \r\n").getBytes());
            }
            
            outStr.write(("Connection: keep-alive\r\n" ).getBytes());
            outStr.write(("SOAPAction: \"\"\r\n" ).getBytes());

            if (logger.isDebugEnabled()) {
                logger.debug( "[Begin Outgoing Request to Relay]\r\n" );
                logger.debug( "POST " + url.getPath() + " HTTP/1.0\r\n" );
                logger.debug( "Host: " + this.ip + "\r\n" );
                logger.debug( "Content-Type: multipart/related; type=\"text/xml\";\r\n" );
                logger.debug( "SOAPAction: \"\"\r\n" );
            }

            if (authHeaderValue != null) {
                outStr.write( ("Authorization: " + authHeaderValue + "\r\n" ).getBytes() );
                if( logger.isDebugEnabled() ) {
                    logger.debug( "Authorization: " + authHeaderValue + "\r\n" );
                }
            }else if(url.getProtocol().equals("https")) {
                authHeaderValue = "Basic " + Base64.encodeString( userName + ":" + password );
                outStr.write( ("Authorization: " + authHeaderValue + "\r\n" ).getBytes() );
                if (logger.isDebugEnabled()) {
                    logger.debug( "Authorization: " + authHeaderValue + "\r\n" );
                }
            }

            request.writeTo(outStr);
            outStr.flush();

            if( logger.isDebugEnabled() ) {
                logger.debug( "[End Outgoing Request to Relay]" );
            }

            return getHTTPResponseCode(socket.getInputStream());

        } catch( IOException ioe ) {
            throw new APIException("io-exception-writing-to-socket", ioe.getMessage());
        } catch( SOAPException se ) {
            throw new APIException( se.getMessage() );
        }
    }

    /**
     *  This method is used to get a single line from the input stream.
     *
     *  @param inStream the input stream to read from.
     *  @return the string read from the input stream.
     *  @exception APIException if there is an io error or if there is no response from server.
     *
     */
    private static String getLine( InputStream inStream )
                                   throws APIException {
        StringBuffer line = new StringBuffer();

        try {
            for( ; ; ) {
                int byteRead = inStream.read();
                if( byteRead == -1 ) break;

                if( ( char ) byteRead == '\r' ) {
                    int newByteRead = inStream.read();
                    if( newByteRead == -1 ) break;

                    if( ( char ) newByteRead == '\n' ) {
                        return line.toString();
                    }

                    break;
                }

                line.append( ( char ) byteRead );
            }
        } catch( IOException e ) {
            throw new APIException( "io-exception-reading-from-socket",
                                    e.getMessage() );
        }

        if( line.length() != 0 )
            throw new APIException( "improperly-formated-line", line );

        return null;
    }

    /**
     *  This method is used to retrieve the HTTP response code from the input stream
     *  after a message has been written to it.
     *
     *  @param inStream the input stream to read from.
     *  @return the HTTP response code.
     *  @exception APIException if there is no response from server or if the returned
     *          response code is not a number.
     *
     */
    private int getHTTPResponseCode(InputStream inStream) throws APIException {
        String firstLine = getLine( inStream );

        if (firstLine == null || firstLine.length() == 0)
            throw new APIException( ErrorCode.SERVER_ERROR, "no-response-from-server" );

        String responseCode = null;
        int responseCodeStart = firstLine.indexOf(' ');

        if (responseCodeStart != -1) {
            int responseCodeEnd = firstLine.indexOf(' ', responseCodeStart + 1);
            responseCode = firstLine.substring(responseCodeStart + 1, responseCodeEnd);

            try {
                return Integer.parseInt(responseCode);

            }catch(NumberFormatException e) {
                throw new APIException(ErrorCode.SERVER_ERROR, "http-response-code-not-number", responseCode);
            }
        }else throw new APIException(ErrorCode.SERVER_ERROR, "http-response-code-not-found");
    }

    /**
     *  Inner class to hold the values from the WWW-Authenticate HTTP header.
     */
    class WWWAuthenticateHeader {
        public String authType; //basic/digest
        public String realm;
        public String algorithm;
        public String qop;
        public String nonce;
    }

    /**
     *  This method is used to retrieve the WWW-Authenticate header from the input stream.
     *
     *  @param inStr the input stream to read from.
     *  @return the WWWAuthenticateHeader object.
     *  @exception APIException if the authenticate header is not found or if it is not properly formatted.
     *
     */
    private WWWAuthenticateHeader getWWWAuthHeader( InputStream inStr )
                                                    throws APIException {
        InternetHeaders headers = null;
        try {
            headers = new InternetHeaders( inStr );
        } catch( MessagingException me ) {
            throw new APIException( "cannot-read-headers",
                                    me.getMessage() );
        }

        String authHeaderValue[] = headers.getHeader( "www-authenticate" );
        if( authHeaderValue == null || authHeaderValue[0] == null ||
            authHeaderValue[0].length() == 0 )
            throw new APIException( ErrorCode.SERVER_ERROR,
                                    "www-authenticate-header-not-found" );

        WWWAuthenticateHeader header = new WWWAuthenticateHeader();
        StringTokenizer parser = new StringTokenizer( authHeaderValue[0] );
        if( parser.hasMoreTokens() ) {
            header.authType = parser.nextToken().toLowerCase();
        }
        while( parser.hasMoreTokens() ) {
            String token = parser.nextToken( ", " );
            int equalsIndex = token.indexOf( '=' );
            if( equalsIndex == -1 )
                throw new APIException( ErrorCode.SERVER_ERROR,
                                        "bad-www-authenticate-header" );
            String key = token.substring( 0, equalsIndex ).toLowerCase();
            String value = token.substring( equalsIndex + 2 , token.length() - 1 );
            if( key.equals( "realm" ) ) {
                header.realm = value;
            } else if( key.equals( "nonce" ) ) {
                header.nonce = value;
            } else if( key.equals( "qop" ) ) {
                header.qop = value;
            } else if( key.equals( "algorithm" ) ) {
                header.algorithm = value;
            }
        }
        return header;
    }

    /**
     *  This method is used to compute the HTTP Authorization header value.
     *
     *  @param header the WWW-Athenticate header values encapsulated in a WWWAuthenticateHeader
     *         object.
     *  @exception APIException if authType from the header is not one of basic or digest.
     *
     */
    private String computeAuthHeaderValue( WWWAuthenticateHeader header )
                                           throws APIException {
        if( header.authType.equals( "basic" ) ) {
            return "Basic " + Base64.encodeString( userName + ":" + password );
        } else if( header.authType.equals( "digest" ) ) {
            String nonceCount = "0000001";
            String cNonce = "openwave";
            MessageDigest digest = null;
            try {
                digest = MessageDigest.getInstance( "MD5" );
            } catch( NoSuchAlgorithmException nsae ) {
                throw new APIException( "no-md5" );
            }
            String ha1 = convertDigestToString( digest.digest( ( userName + ":" + header.realm +
                                                                 ":" + password ).getBytes() ) );
            digest.reset();

            String ha2 = convertDigestToString( digest.digest( ( "POST:" + url.getPath() ).getBytes() ) );
            digest.reset();

            String response = convertDigestToString( digest.digest( ( ha1 + ":" +
                                                                      header.nonce + ":" +
                                                                      nonceCount + ":" +
                                                                      cNonce + ":" +
                                                                      header.qop + ":" +
                                                                      ha2 ).getBytes() ) );
            return "Digest " + "username=\"" + userName + "\"," +
                               "realm=\"" + header.realm + "\"," +
                               "response=\"" + response + "\"," +
                               "nonce=\"" + header.nonce + "\"," +
                               "cnonce=\"" + cNonce + "\"," +
                               "nc=\"" + nonceCount + "\"," +
                               "qop=\"" + header.qop + "\"," +
                               "uri=\"" + url.getPath() + "\"";
        } else throw new APIException( ErrorCode.SERVER_ERROR,
                                       "unknown-authtype", header.authType );
    }

    private String convertDigestToString( byte[] digest ) {
        StringBuffer buf = new StringBuffer( 32 );
        for( int i = 0; i < 16; i++ ) {
            String hex = Integer.toHexString( digest[i] );
            if( hex.length() > 2 ) hex = hex.substring( 6 );
            if( hex.length() < 2 ) hex = "0" + hex;
            buf.append( hex );
        }
        return buf.toString();
    }

    private static void writeHeaders( OutputStream out )
                                      throws IOException {
        out.write( "HTTP/1.0 200 OK\r\n".getBytes() );
        out.write( "Content-Type: text/xml\r\n".getBytes() );

        if( logger.isDebugEnabled() ) {
            logger.debug( "HTTP/1.0 200 OK\r\n" );
            logger.debug( "Content-Type: text/xml\r\n" );
        }
    }

    private void writeEmptyResponse( OutputStream out ) throws IOException {
        writeHeaders( out );
        out.write( "Content-length: 0\r\n\r\n".getBytes() );

        if( logger.isDebugEnabled() ) {
            logger.debug( "Content-length: 0\r\n\r\n" );
        }
    }

    /**
     *  This method is called at the beginning of every public method to enforce threading policy.
     *  Only the thread that created the RelayConnection object is allowed to use it.
     *
     *  @exception APIException if thread policy if not followed.
     *
     */
    private void threadCheck() throws APIException {
        if( ! owningThread.equals( Thread.currentThread() ) )
            throw new APIException( ErrorCode.THREADING_ERROR,
                                    "thread-policy" );
    }

    static {
        try {
            // always there. shipped with the api
            String propFileName = "/resources/RelayConnection.properties";
            InputStream propFile = RelayConnection.class.getResourceAsStream( propFileName );

            Properties p = new Properties( System.getProperties() );
            p.load( propFile );

            // set the system properties
            System.setProperties( p );
        } catch( IOException e ) {
            e.printStackTrace();
        }
    }

    private static void writeResponse(Response res, OutputStream outputStream ) throws IOException, SOAPException {
        if( logger.isDebugEnabled() ) {
            logger.debug( "[Begin Outgoing Response To Relay]" );
        }
        writeHeaders( outputStream );
        res.writeTo( outputStream );
        if( logger.isDebugEnabled() ) {
            logger.debug( "[End Outgoing Response To Relay]" );
        }
    }

    private static void writeServletResponse( Response res,
                                              ServletResponse response )
                                              throws IOException,
                                                     SOAPException {
        if( logger.isDebugEnabled() ) {
            logger.debug( "[Begin Outgoing Response To Relay]" );
        }
        response.setContentType( "text/xml" );

        if( res != null )
            res.writeTo( response );

        if( logger.isDebugEnabled() ) {
            logger.debug( "[End Outgoing Response To Relay]" );
        }
    }

    private static final Logger logger = Logger.getLogger( RelayConnection.class );

    // connection modes
    private static final int RECEIVER_ONLY = 0;
    private static final int SENDER_ONLY = 1;
    private static final int SENDER_AND_RECEIVER = 2;

    private String ip = null;
    private int mode;
    private int port;
    private URL url;
    private boolean weakCN = false;
    private String userName;
    private String password;
    private Server server;
    private OutputStream debugOutputStream;
    private OutputStream logOutputStream;
    private HashMap authenticators;
    private AuthenticationType authType = AuthenticationType.ANY;
    private MessageListener messageListener;
    private Thread owningThread;

}
/* important prop info.
Only in 1.4?
Proxy support:

http.proxyHost (default: <none>)
http.proxyPort (default: 80 if http.proxyHost specified)
http.nonProxyHosts (default: <none>

http.proxyHost and http.proxyPort indicate the proxy server and port that the http protocol handler will use. 
http.nonProxyHosts indicates the hosts which should be connected too directly and not through the proxy server. The value can be a list of hosts, each seperated by a |, and in addition a wildcard character (*) can be used for matching. For example: -Dhttp.nonProxyHosts="*.foo.com|localhost". 

Digest Auth support:

http.auth.digest.validateServer (default: false)
http.auth.digest.validateProxy (default: false)
http.auth.digest.cnonceRepeat (default: 5) 

These system properties modify the behavior of the HTTP digest authentication mechanism. Digest authentication provides a limited ability for the server to authenticate itself to the client (ie. by proving that it knows the users password). Haever, not all servers support this capability and by default the check is switched off. The first two properties above can be set to true, to enforce this check, for either authentication with an origin, or a proxy server respectively. 
It is not normally necessary to set the third property (http.auth.digest.cnonceRepeat). This determines how many times a cnonce value is reused. This can be useful when the MD5-sess algorithm is being used. Increasing the value reduces the computational overhead on both the client and the server by reducing the amount of material that has to be hashed for each HTTP request. 

*/
