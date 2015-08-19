package com.openwave.mms.mm7.util;

import java.util.Enumeration;
import java.io.InputStream;
import java.io.IOException;
import javax.mail.internet.InternetHeaders;
import javax.mail.MessagingException;

public class RecordedInputStream extends InputStream {

    public RecordedInputStream( InputStream inStream,
                                int contentLength )
                                throws IOException {
        text = new StringBuffer();
        byte[] buf = new byte[4096];
        int totalBytesRead = 0;
        int bytesRead;
        while( totalBytesRead < contentLength ) {
            bytesRead = inStream.read( buf );
            if(bytesRead == -1 ) break;
            text.append( new String( buf, 0, bytesRead ) );
            totalBytesRead += bytesRead;
        }
    }

    public RecordedInputStream( InputStream inStream )
                                throws IOException, MessagingException {
        text = new StringBuffer();

        InternetHeaders headers = new InternetHeaders( inStream );
        
        Enumeration enume = headers.getAllHeaderLines();
        while( enume.hasMoreElements() ) {
            text.append( ( String ) enume.nextElement() );
            text.append( "\r\n" );
        }

        text.append( "\r\n" );

        int contentLength = 0;
        try {
            String[] contentLengthStr = headers.getHeader( "content-length" );
            if( contentLengthStr != null &&
                contentLengthStr[0] != null )
                contentLength = Integer.parseInt( contentLengthStr[0] );
        } catch( NumberFormatException nfe ) {}

        byte[] buf = new byte[4096];
        int totalBytesRead = 0;
        int bytesRead;
        while( totalBytesRead < contentLength ) {
            bytesRead = inStream.read( buf );
            if(bytesRead == -1 ) break;
            text.append( new String( buf, 0, bytesRead ) );
            totalBytesRead += bytesRead;
        }
    }

    public int available() {
        return text.length() - readOffset;
    }

    public int read( byte[] buffer, int offset, int length ) {
        byte[] localBuffer = new byte[length];
        int bytesRead = read( localBuffer );
        if( bytesRead == -1 ) return -1;
        System.arraycopy( localBuffer,
                          0,
                          buffer,
                          offset,
                          bytesRead );
        return bytesRead;
    }

    public int read( byte[] buffer ) {
        int outputBufferLength = buffer.length;
        int bytesRemaining = text.length() - readOffset;
        if( bytesRemaining == 0 ) return -1;
        int bytesSent = bytesRemaining < outputBufferLength ?
                                         bytesRemaining     :
                                         outputBufferLength ;
        System.arraycopy( text.toString().getBytes(),
                          readOffset,
                          buffer,
                          0,
                          bytesSent );
        readOffset += bytesSent;
        return bytesSent;
    }

    public int read() {
        if( text.length() == readOffset ) return -1;

        char nextChar = text.charAt( readOffset );
        readOffset++;

        return nextChar;
    }

    public String getBuffer() {
        /*
        StringBuffer copy = new StringBuffer( 4096 );
        for( int i = 0; i < text.length(); i++ ) {
            if( text.charAt( i ) == '\r' )
                copy.append( "\\r" );
            else if( text.charAt( i ) == '\n' )
                copy.append( "\\n\n" );
            else copy.append( text.charAt( i ) );
        }
        return copy.toString();
        */
        return text.toString();
    }

    private StringBuffer text;
    private int readOffset;
}

