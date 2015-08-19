package com.openwave.mms.mm7.soap;

import java.util.Iterator;
import java.util.Vector;

public class SOAPHeader extends SOAPParameter {

    public SOAPHeader() throws SOAPException {
        super( new SOAPQName( SOAPConsts.SOAPEnvPrefix,
                              SOAPConsts.SOAPHeader, null ),
               null, (Vector) null );
    }

    public void addHeader( SOAPParameter param )
                           throws SOAPException {
        if( param != null )
            addParameter( param );
    }

    public void addHeader( String headerName,
                           String headerValue )
                           throws SOAPException {
        addParameter( headerName, headerValue );
    }

    public void addHeader( String uri,
                           String headerName,
                           String headerValue )
                           throws SOAPException {
        SOAPQName headerQName = new SOAPQName( headerName,
                                               SOAPConsts.SOAPMM7Prefix,
                                               uri );
        SOAPParameter param = new SOAPParameter( headerQName,
                                                 null, (Vector) null );
        param.setValue( headerValue );
        addParameter( param );
    }

    
	/*
	 *  Creado por Andres Navarro
	 */
	public void addHeader(String uri, String headerName, String headerValue, String customSOAPMM7Prefix) throws SOAPException {
		SOAPQName headerQName = new SOAPQName(headerName, customSOAPMM7Prefix, uri);
		SOAPParameter param = new SOAPParameter(headerQName, null, (Vector) null);
		param.setValue(headerValue);
		addParameter(param);
	}

    public String getHeaderValue( String headerName )
                                  throws SOAPException {
        return getValue( headerName );
    }

    public Vector getHeaders() { return parameters; }

    public void serialize( StringBuffer buffer ) {
        if( parameters != null && !parameters.isEmpty() ) {
            buffer.append( "<" );
            buffer.append( SOAPConsts.SOAPEnvPrefix );
            buffer.append( ":" );
            buffer.append( SOAPConsts.SOAPHeader );
            buffer.append( ">" );
            for( int i = 0; i < parameters.size(); i++ ) {
                SOAPParameter param = (SOAPParameter) parameters.get( i );
                param.serialize( buffer );
            }
            buffer.append( "</" );
            buffer.append( SOAPConsts.SOAPEnvPrefix );
            buffer.append( ":" );
            buffer.append( SOAPConsts.SOAPHeader );
            buffer.append( ">" );
        }
    }

}
