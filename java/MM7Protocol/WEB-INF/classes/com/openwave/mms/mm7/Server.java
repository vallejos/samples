package com.openwave.mms.mm7;

import java.util.HashMap;
import java.io.IOException;
import java.io.OutputStream;
import java.net.ServerSocket;
import javax.net.ServerSocketFactory;
import javax.net.ssl.SSLServerSocket;
import javax.net.ssl.SSLServerSocketFactory;

class Server {

    public Server( RelayConnection conn,
                   int port,
                   boolean secure ) throws APIException {
        try {
            ServerSocketFactory ssf =
                Server.getServerSocketFactory( secure ? "SSL" : "" );
            ServerSocket serverSocket = ssf.createServerSocket( port );
            if( secure ) {
                SSLServerSocket sslServerSocket = ( SSLServerSocket ) serverSocket;
                sslServerSocket.setEnabledCipherSuites( sslServerSocket.getSupportedCipherSuites() );
            }
            listener = new Listener( conn, serverSocket );
        } catch( IOException e ) {
            throw new APIException( "cannot-start-listener",
                                    e.getMessage() );
        }
    }

    public void startListener( ) {
        listener.startListener( );
    }

    public void setAuthenticators( HashMap authenticators ) {
        listener.setAuthenticators( authenticators );
    }

    public void setAuthType( RelayConnection.AuthenticationType authType ) {
        listener.setAuthType( authType );
    }

    private static ServerSocketFactory getServerSocketFactory( String type ) {
        if( type.equals( "SSL" ) ) {
            return SSLServerSocketFactory.getDefault();
        } else {
            return ServerSocketFactory.getDefault();
        }
    }

    private Listener listener;

}
