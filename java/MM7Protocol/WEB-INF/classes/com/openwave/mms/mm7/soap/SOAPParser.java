package com.openwave.mms.mm7.soap;

import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.io.PushbackInputStream;
import java.util.HashMap;
import java.util.Map;

public class SOAPParser {
    public SOAPParser( InputStream inputStream )
                       throws IOException, SOAPException {
        globalNamespaces = new HashMap();
        scanner = new Scanner( inputStream );
        SOAPQName envelopeName = new SOAPQName();
        scanner.eatBeginTag( envelopeName );

        if ( envelopeName.getName() == null ) {
            throw new SOAPException( "envelope-begin-tag-missing" );
        }

        if ( ! envelopeName.getName()
                           .equalsIgnoreCase( SOAPConsts.SOAPEnvelope ) ) {
            throw new SOAPException( "envelope-name-mismatch" );
        }
        envelope = new SOAPEnvelope( null );
        SOAPQName headerName = new SOAPQName();
        SOAPQName bodyName = new SOAPQName();
        scanner.eatBeginTag( headerName );

        SOAPHeader header = null;
        if( headerName.getName()
                      .equalsIgnoreCase( SOAPConsts.SOAPHeader ) ) {
            header = new SOAPHeader( );
            scanArgs( header );
            envelope.setHeader( header );

            scanner.eatEndTag( headerName.getName() );
            scanner.eatBeginTag( bodyName );
        } else {
            bodyName = headerName;
        }

        if( bodyName.getName() == null ) {
            throw new SOAPException( "body-begin-tag-missing" );
        }

        if( ! bodyName.getName().equalsIgnoreCase( SOAPConsts.SOAPBody ) ) {
            throw new SOAPException( "body-name-mismatch" );
        }
        SOAPBody body = new SOAPBody( );
        envelope.setBody( body );

        SOAPQName methodName = new SOAPQName();
        scanner.eatBeginTag( methodName );
        if( methodName.getName() == null ) {
            throw new SOAPException( "method-missing" );
        }
        SOAPMethod method = new SOAPMethod( methodName, null );
        scanArgs( method );
        scanner.eatEndTag( methodName.getName() );
        body.setMethod( method );

        scanner.eatEndTag( bodyName.getName() );
        scanner.eatEndTag( envelopeName.getName() );
    }

    private class Scanner {
        public Scanner( InputStream inputStream ) throws IOException {
            inStream = new PushbackInputStream( inputStream, 2 );
            // ignore optional xml prolog <?xml version='1.0' ?>
            if( inStream.available() > 1 ) {
                int byteReadOne = inStream.read();

                if( ( char ) byteReadOne == '<' ) {
                    int byteReadTwo = inStream.read();

                    if( ( char ) byteReadTwo == '?' ) {
                        eatUntil( '>', null, true );
                        return;
                    }

                    inStream.unread( byteReadTwo );
                }

                inStream.unread( byteReadOne );
            }
        }

        public boolean eatBeginTag( SOAPQName tag )
                                    throws IOException, SOAPException {
            String nextTag = eatNextTag( true );

            if( nextTag == null ) {
                return false;
            }

            int startName = 1;
            int colon = nextTag.indexOf( ":", startName );
            int space = nextTag.indexOf( " ", startName );
            String prefix = null;

            if( colon != -1 ) {
                if( space == -1 || ( space != -1 && colon < space ) ) {
                    startName = colon + 1;
                    prefix = nextTag.substring( 1, colon );
                    tag.setPrefix( prefix );
                }
            }

            int stopName = nextTag.length() - 1;

            if( space != -1 ) {
                stopName = space;
            }

            tag.setName( nextTag.substring( startName, stopName ) );

            String xmlns = ( prefix == null ) ? "xmlns"
                                              : "xmlns:" + prefix;
            int namespaceAttrIndex = nextTag.indexOf( xmlns, stopName );
            if( namespaceAttrIndex != -1 ) {
                // we have the namespace here
                int namespaceStart = namespaceAttrIndex +
                                     xmlns.length() + "=\"".length();
                int namespaceEnd = nextTag.indexOf( "\"", namespaceStart );
                String namespace = nextTag.substring( namespaceStart,
                                                      namespaceEnd );
                tag.setUri( namespace );
                if( prefix != null ) {
                    globalNamespaces.put( prefix, namespace );
                }
            } else if( prefix != null ) {
                tag.setUri( ( String ) globalNamespaces.get( prefix ) );
            }
            return nextTag.indexOf( "/" ) == ( nextTag.length() - 2 );
        }

        public void eatEndTag( final String tagName )
                               throws IOException, SOAPException {
            String nextTag = eatNextTag( false );

            if( nextTag == null ) {
                throw new SOAPException( "soap-end-tag-missing" );
            }

            if( nextTag.charAt( 1 ) != '/' ) {
                throw new SOAPException( "soap-end-tag-missing" );
            }

            int startName = 2;
            int colon = nextTag.indexOf( ":", startName );

            if( colon != -1 ) {
                startName = colon + 1;
            }

            int stopName = nextTag.length() - 1;
            int space = nextTag.indexOf( " ", startName );

            if( space != -1 ) {
                stopName = space;
            }

            String endTagName = nextTag.substring( startName,
                                                   stopName );

            if( ! tagName.equalsIgnoreCase( endTagName ) ) {
                throw new SOAPException( "soap-end-tag-mismatch" );
            }
        }

        public String eatNextTag( boolean beginOnly )
                                  throws IOException, SOAPException {
            boolean startFound = eatUntil( '<', null, false );

            if( ! startFound ) {
                return null;
            }

            if( beginOnly ) {
                boolean endtagNext = false;

                if( inStream.available() > 1 ) {
                    int byteReadOne = inStream.read();

                    if( ( char ) byteReadOne == '<' ) {
                        int byteReadTwo = inStream.read();

                        if( ( char ) byteReadTwo == '/' ) {
                            endtagNext = true;
                        }

                        inStream.unread( byteReadTwo );
                    }

                    inStream.unread( byteReadOne );
                    if( endtagNext ) return null;
                }
            }

            StringBuffer tagValue = new StringBuffer();
            boolean endFound = eatUntil( '>', tagValue, true );

            if( ! endFound ) {
                throw new SOAPException( "soap-tag-not-closed" );
            }

            return tagValue.toString();
        }

        public boolean eatToNextTag( StringBuffer argValue )
                                     throws IOException, SOAPException {
            boolean startFound = eatUntil( '<', argValue, false );

            if( ! startFound ) {
                throw new SOAPException( "soap-tag-not-closed" );
            }

            boolean endtagNext = false;

            if( inStream.available() > 1 ) {
                int byteReadOne = inStream.read();

                if( ( char ) byteReadOne == '<' ) {
                    int byteReadTwo = inStream.read();

                    if( ( char ) byteReadTwo == '/' ) {
                        endtagNext = true;
                    }
                    inStream.unread( byteReadTwo );
                }
                inStream.unread( byteReadOne );
            }

            return endtagNext;
        }

        private boolean eatUntil( char token,
                                  StringBuffer text,
                                  boolean eatToken )
                                  throws IOException {
            int byteRead;
            while( ( byteRead = inStream.read() ) != -1 ) {

                if( ( char ) byteRead == token ) {

                    if( ! eatToken ) {
                        inStream.unread( byteRead );
                    } else {
                        if( text != null ) {
                            text.append( ( char ) byteRead );
                        }
                    }
                    return true;
                }

                if( text != null ) {
                    text.append( ( char ) byteRead );
                }
            }

            return false;
        }

        private PushbackInputStream inStream;
    }

    public Scanner scanner;

    public void scanArgs( SOAPParameter param ) throws IOException, SOAPException {
        for ( ; ; ) {
            SOAPQName argName = new SOAPQName();
            boolean thisIsEndTag = scanner.eatBeginTag( argName );
            if( argName.getName() == null ) {
                return;
            }
            SOAPParameter newParam = new SOAPParameter( argName );
            if( !thisIsEndTag ) {
                StringBuffer argValue = new StringBuffer();
                boolean endTagNext = scanner.eatToNextTag( argValue );
  
                if( endTagNext ) {
                    if( argValue.length() > 0 ) {
                        newParam.setValue( argValue.toString() );
                    }
                } else {
                    scanArgs( newParam );
                }
                scanner.eatEndTag( argName.getName() );
            }
   
            param.addParameter( newParam );
        }
    }

    public SOAPEnvelope getEnvelope() { return envelope; }

    Map globalNamespaces;
    private SOAPEnvelope envelope;
}
