package com.openwave.mms.mm7.soap;

import java.util.HashMap;
import java.util.Map;
import java.util.Iterator;

public class SOAPEnvelope {

    private SOAPHeader header;
    private SOAPBody body;
    private HashMap namespaces;

    public SOAPEnvelope( HashMap namespaces )
                         throws SOAPException {
        header = new SOAPHeader();
        body = new SOAPBody();
        this.namespaces = namespaces;
    }

    public void setHeader( SOAPHeader header ) { this.header = header; }
    public SOAPHeader getHeader() { return header; }

    public void setBody( SOAPBody body ) { this.body = body; }
    public SOAPBody getBody() { return body; }

    public String toString() {
        StringBuffer buffer = new StringBuffer();
        buffer.append( "<?xml version=\'1.0\' ?>" );
        buffer.append( "<" );
        buffer.append( SOAPConsts.SOAPEnvPrefix );
        buffer.append( ":" );
        buffer.append( SOAPConsts.SOAPEnvelope );
        addNamespace( buffer,
                      SOAPConsts.SOAPEnvPrefix,
                      SOAPConsts.SOAPNamespace );
        if( namespaces != null ) {
            Iterator iter = namespaces.entrySet().iterator();
            while( iter.hasNext() ) {
                Map.Entry entry = (Map.Entry) iter.next();
                addNamespace( buffer,
                              (String) entry.getKey(),
                              (String) entry.getValue() );
            }
        }
        buffer.append( ">" );

        header.serialize( buffer );
        body.serialize( buffer );

        buffer.append( "</" );
        buffer.append( SOAPConsts.SOAPEnvPrefix );
        buffer.append( ":" );
        buffer.append( SOAPConsts.SOAPEnvelope );
        buffer.append( ">" );

        return buffer.toString();
    }

    private void addNamespace( StringBuffer buffer,
                               String namespacePrefix,
                               String namespaceUri ) {
        buffer.append( " " );
        buffer.append( "xmlns:" );
        buffer.append( namespacePrefix );
        buffer.append( "=\"" );
        buffer.append( namespaceUri );
        buffer.append( "\"" );
    }
}
