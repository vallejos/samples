package com.openwave.mms.mm7;

import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.ServerSocket;
import java.net.Socket;
import javax.mail.internet.InternetHeaders;
import javax.mail.internet.MimeMultipart;
import javax.mail.MessagingException;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.io.PrintStream;
import java.util.StringTokenizer;
import java.util.Random;
import java.util.HashMap;

import org.apache.log4j.Logger;
import org.apache.log4j.Level;
import org.apache.log4j.NDC;

import com.openwave.mms.mm7.util.Base64;
import com.openwave.mms.mm7.util.InputStreamDataSource;
import com.openwave.mms.mm7.util.RecordedInputStream;
import com.openwave.mms.mm7.soap.SOAPConsts;
import com.openwave.mms.mm7.soap.SOAPEnvelope;
import com.openwave.mms.mm7.soap.SOAPException;
import com.openwave.mms.mm7.soap.SOAPHeader;
import com.openwave.mms.mm7.soap.SOAPMethod;
import com.openwave.mms.mm7.soap.SOAPParser;

class Listener implements Runnable {

    public Listener( RelayConnection relayConn, ServerSocket server ) {
        this.relayConn = relayConn;
        this.server = server;
        secret = new byte[20];
        new Random().nextBytes( secret );
    }

    public synchronized void startListener( ) {
        newListener();    
    }

    public synchronized void setAuthenticators( HashMap authenticators ) {
        this.authenticators = authenticators;
    }

    public synchronized void setAuthType( RelayConnection.AuthenticationType authType ) {
        this.authType = authType;
    }

    public void run() {
        Socket socket = null;

        // accept a connection
        try {
            if( logger.isInfoEnabled() ) {
                logger.info( "MM7Receiver listening for connections on port " +
                             server.getLocalPort() );
            }
            socket = server.accept();
            socket.setSoTimeout( 15 * 1000 ); // 15 secs
        } catch( IOException e ) {
            try {
                if( logger.isEnabledFor( Level.WARN ) ) {
                    logger.warn( e.getMessage() );
                }
                if( socket != null )
                    socket.close();
            } catch( IOException ioe ) {
                //cant do much!
                if( logger.isEnabledFor( Level.WARN ) ) {
                    logger.warn( ioe.getMessage() );
                }
            }
            NDC.remove();
            return;
        }

        // create a new thread to accept the next connection
        newListener();

        try {
            if( logger.isInfoEnabled() ) {
                logger.info( "MM7Receiver got a connection" );
            }

            OutputStream out = socket.getOutputStream();
            try {
                InputStream in = socket.getInputStream();

                if( logger.isInfoEnabled() ) {
                    RecordedInputStream recorder = new RecordedInputStream( in );
                    logger.debug( "[Begin Incoming Request From Relay]" );
                    logger.debug( recorder.getBuffer() );
                    logger.debug( "[End Incoming Request From Relay]" );
                    in = recorder;
                }
                InternetHeaders headers = new InternetHeaders( );

                if( authenticators != null ) {
                    boolean authSuccess = checkAuth( in, out, headers );
                    if( ! authSuccess ) return;
                } else headers.load( in );

                // the following should be part of a dispatcher class
                // which can be used in a Servlet
                String contentTypes[] = headers.getHeader( "content-type" );
                Response response = RelayConnection.dispatchInternal( in,
                                                                      contentTypes[0],
                                                                      relayConn.getMessageListener() );
                writeResponse( response, out );

            } catch( APIException ae ) {
                if( logger.isEnabledFor( Level.WARN ) ) {
                    logger.warn( ae.getLocalizedMessage() );
                }
                FaultResponse res = new FaultResponse( ae.getMessage(),
                                                       ae.getErrorCode() );
                writeResponse( res, out );
            } catch( IOException ioe ) {
                if( logger.isEnabledFor( Level.WARN ) ) {
                    logger.warn( ioe.getMessage() );
                }
                // write out error response
                FaultResponse res = new FaultResponse( ioe.getMessage(),
                                                       ErrorCode.SERVER_ERROR );
                writeResponse( res, out );
            } catch( MessageProcessingException mpe ) {
                if( logger.isEnabledFor( Level.WARN ) ) {
                    logger.warn( mpe.getLocalizedMessage() );
                }
                // write dummy response
                FaultResponse res = new FaultResponse( "Client",
                                                       "Client Error",
                                                       mpe.getMessage(),
                                                       mpe.getErrorCode() );
                writeResponse( res, out );
            }
            out.flush();
        } catch( Exception ex ) {
            // catch all. most probably this exception occurred when processing
            // other exceptions so just log it
            if( logger.isEnabledFor( Level.WARN ) ) {
                logger.warn( ex.getMessage() );
            }
        } finally {
            try {
                socket.close();
            } catch( IOException ioe ) {
                //cant do much!
                if( logger.isEnabledFor( Level.WARN ) ) {
                    logger.warn( ioe.getMessage() );
                }
            }
            NDC.remove();
        }
    }

    private void writeHeaders( OutputStream out )
                               throws IOException {
        out.write("HTTP/1.0 200 OK\r\n".getBytes());
        out.write("Content-Type: text/xml\r\n".getBytes());

        if( logger.isDebugEnabled() ) {
            logger.debug( "[Begin Outgoing Response To Relay]\n" );
            logger.debug( "HTTP/1.0 200 OK\r\n" );
            logger.debug( "Content-Type: text/xml\r\n" );
        }
    }

    private void newListener() {
        (new Thread(this)).start();
    }

    private void writeEmptyResponse( OutputStream outStream )
                                     throws IOException {
        writeHeaders( outStream );
        outStream.write( "Content-length: 0\r\n\r\n".getBytes() );

        if( logger.isDebugEnabled() ) {
            logger.debug( "Content-length: 0\r\n\r\n".getBytes() );
            logger.debug( "\n[End Outgoing Response To Relay]" );
        }
    }

    class Authorization {
        public String userName;
        public String realm;
        public String nonce;
        public String cNonce;
        public String nonceCount;
        public String response;
        public String qop;
        public String uri;
    }

    private boolean checkAuth( InputStream inStream,
                               OutputStream outStream,
                               InternetHeaders headers )
                               throws APIException, IOException, MessagingException {
        headers.load( inStream );
        String authHeaders[] = headers.getHeader( "authorization" );
        String authHeader = null;
        if( authHeaders != null &&
            ( authHeader = authHeaders[ authHeaders.length - 1 ] ) != null ) {
            //parse the header and verify Auth
            int spaceIndex = authHeader.indexOf( " " );
            if( spaceIndex == -1 )
                throw new APIException( ErrorCode.CLIENT_ERROR,
                                        "malformed-authorization-header" );
            String authType = authHeader.substring( 0, spaceIndex );

            if( authType.equalsIgnoreCase( "basic" ) ) {
                if( this.authType == RelayConnection.AuthenticationType.DIGEST ) {
                    drainInputStream( inStream, headers );
                    sendAuthHeader( outStream );
                    return false;
                }
                String base64String = authHeader.substring( spaceIndex + 1 );
                byte[] decodedString = Base64.decode( base64String );
                boolean colonFound = false;
                StringBuffer userName = new StringBuffer();
                StringBuffer password = new StringBuffer();
                for( int i = 0; i < decodedString.length; i++ ) {
                    if( (char)decodedString[i] == ':' ) {
                        colonFound = true;
                        continue;
                    }
                    if( (char)decodedString[i] == '\r' || (char)decodedString[i] == '\n' )
                        continue;
                    if( colonFound )
                        password.append( (char)decodedString[i] );
                    else
                        userName.append( (char)decodedString[i] );
                }
                java.net.PasswordAuthentication auth = ( java.net.PasswordAuthentication )
                                                       authenticators.get( userName.toString() );
                if( ! new String( auth.getPassword() ).equals( password.toString() ) )
                    throw new APIException( ErrorCode.SERVICE_DENIED,
                                            "unauthorized-user", userName );
            } else if( authType.equalsIgnoreCase( "digest" ) ) {
                String digestHeaderValue = authHeader.substring( spaceIndex + 1 );
                AuthorizationHeader header = new AuthorizationHeader( digestHeaderValue );
                String ourHashValue = computeHash( header );
                if( !header.response.equals( ourHashValue ) )
                    throw new APIException( ErrorCode.SERVICE_DENIED,
                                            "unauthorized-user", header.userName );
            } else throw new APIException( ErrorCode.CLIENT_ERROR,
                                           "unknown-authtype" );
            return true;
        } else {
            //no auth info supplied, so send him a WWW-Authenticate header
            drainInputStream( inStream, headers );
            sendAuthHeader( outStream );
        }

        return false;
    }

    private void sendAuthHeader( OutputStream outStream )
                                 throws IOException, APIException {
        if( this.authType == RelayConnection.AuthenticationType.BASIC ) {
            if( logger.isDebugEnabled() ) {
                NDC.push( "unauthorized" );
                logger.debug( "[Begin Outgoing Response To Relay]\n" );
                logger.debug( "HTTP/1.1 401 Unauthorized\r\n" +
                              "Connection: Keep-Alive\r\n" +
                              "WWW-Authenticate: Basic realm=\"openwavemm7\"\r\n\r\n" );
                logger.debug( "\n[End Outgoing Response To Relay]" );
                NDC.pop();
            }

            outStream.write( "HTTP/1.1 401 Unauthorized\r\n".getBytes() );
            outStream.write( ( "Connection: Keep-Alive\r\n" ).getBytes() );
            outStream.write( ( "WWW-Authenticate: Basic realm=\"openwavemm7\"\r\n\r\n" ).getBytes() );
            outStream.flush();
        } else {
            String nonce = genNonce();
            if( logger.isDebugEnabled() ) {
                NDC.push( "unauthorized" );
                logger.debug( "[Begin Outgoing Response To Relay]\n" );
                logger.debug( "HTTP/1.1 401 Unauthorized\r\n" +
                              "Connection: close\r\n" +
                              "WWW-Authenticate: Digest realm=\"openwavemm7\"," +
                              "algorithm=\"MD5\",qop=\"auth\",nonce=\"" +
                              nonce + "\"\r\n\r\n" );
                logger.debug( "\n[End Outgoing Response To Relay]" );
                NDC.pop();
            }

            outStream.write( "HTTP/1.1 401 Unauthorized\r\n".getBytes() );
            outStream.write( ( "Connection: Keep-Alive\r\n" ).getBytes() );
            outStream.write( ( "WWW-Authenticate: Digest realm=\"openwavemm7\"," +
                               "algorithm=\"MD5\",qop=\"auth\",nonce=\"" +
                               nonce + "\"\r\n\r\n" ).getBytes() );
            outStream.flush();
        }
    }

    // Base64(Time)+SHA(Secret+realm+time)
    private String genNonce() throws APIException {
        Long now = new Long( System.currentTimeMillis() );
        String timeBase64 = new String( Base64.encodeString( now.toString() )).trim();
        MessageDigest digest = null;
        try {
            digest = MessageDigest.getInstance( "SHA" );
        } catch( NoSuchAlgorithmException nsae ) {
            throw new APIException( ErrorCode.SERVER_ERROR,
                                   "no-sha" );
        }

        digest.update( secret );
        digest.update( "openwavemm7".getBytes() );
        digest.update( timeBase64.getBytes() );
        byte[] baDigest = digest.digest();

        return timeBase64 + convertDigestToString( baDigest );
    }

    class AuthorizationHeader {
        public String userName;
        public String realm;
        public String response;
        public String nonce;
        public String uri;
        public String nonceCount;
        public String cNonce;
        public String qop;

        public AuthorizationHeader( String authHeaderValue )
                                    throws APIException {
            StringTokenizer parser = new StringTokenizer( authHeaderValue, ", " );
            String token = null;
            while( parser.hasMoreTokens() ) {
                token = parser.nextToken( );
                int equalsIndex = token.indexOf( '=' );
                if( equalsIndex == -1 )
                    throw new APIException( ErrorCode.CLIENT_ERROR,
                                            "bad-authorization-header" );
                String key = token.substring( 0, equalsIndex ).toLowerCase();
                String value = token.substring( equalsIndex + 2 , token.length() - 1 );
                if( key.equals( "realm" ) ) {
                    realm = value;
                } else if( key.equals( "response" ) ) {
                    response = value;
                } else if( key.equals( "nonce" ) ) {
                    nonce = value;
                } else if( key.equals( "qop" ) ) {
                    qop = value;
                } else if( key.equals( "username" ) ) {
                    userName = value;
                } else if( key.equals( "cnonce" ) ) {
                    cNonce = value;
                } else if( key.equals( "uri" ) ) {
                    uri = value;
                } else if( key.equals( "nc" ) ) {
                    nonceCount = value;
                }
            }
        }
    }

    private String computeHash( AuthorizationHeader header )
                                throws APIException {
        java.net.PasswordAuthentication auth = ( java.net.PasswordAuthentication )
                                               authenticators.get( header.userName );
        if( auth == null )
            throw new APIException( "unauthorized-user", header.userName );

        MessageDigest digest = null;
        try {
            digest = MessageDigest.getInstance( "MD5" );
        } catch( NoSuchAlgorithmException nsae ) {
            throw new APIException( ErrorCode.SERVER_ERROR,
                                    "no-md5" );
        }

        String ha1 = convertDigestToString( digest.digest( ( header.userName + ":" + header.realm +
                                                             ":" + new String( auth.getPassword() ) )
                                                  .getBytes() ) );
        digest.reset();
        String ha2 = convertDigestToString( digest.digest( ( "POST:" + header.uri ).getBytes() ));
        digest.reset();
        String response = convertDigestToString( digest.digest( ( ha1 + ":" +
                                                                  header.nonce + ":" +
                                                                  header.nonceCount + ":" +
                                                                  header.cNonce + ":" +
                                                                  header.qop + ":" +
                                                                  ha2 ).getBytes() ) );
        return response;
    }

    private String convertDigestToString( byte[] digest ) {
        // md5 digests are always 16 bytes. this function divides each byte
        // into two and converts them to hex chars
        StringBuffer buf = new StringBuffer( 32 );
        for( int i = 0; i < 16; i++ ) {
            String hex = Integer.toHexString( digest[i] );
            if( hex.length() > 2 ) hex = hex.substring( 6 );
            if( hex.length() < 2 ) hex = "0" + hex;
            buf.append( hex );
        }
        return buf.toString();
    }

    private void drainInputStream( InputStream inStream,
                                   InternetHeaders headers )
                                   throws IOException, APIException {
        String[] contentLength = headers.getHeader( "content-length" );
        if( contentLength == null || contentLength[0].length() == 0 )
            inStream.skip( inStream.available() );
        else {
            try {
                inStream.skip( Integer.parseInt( contentLength[0] ) );
            } catch( NumberFormatException nfe ) {
                throw new APIException( "content length header value NAN" );
            }
        }
    }

    private void writeResponse( Response res,
                                OutputStream outputStream ) throws IOException,
                                                                   SOAPException {
        writeHeaders( outputStream );
        if( res != null )
            res.writeTo( outputStream );
        if( logger.isDebugEnabled() ) {
            logger.debug( "[End Outgoing Response To Relay]" );
        }
    }


    private static final Logger logger = Logger.getLogger( Listener.class );

    private ServerSocket server;
    private HashMap authenticators;
    private byte[] secret;
    private RelayConnection.AuthenticationType authType;
    private RelayConnection relayConn;

}
