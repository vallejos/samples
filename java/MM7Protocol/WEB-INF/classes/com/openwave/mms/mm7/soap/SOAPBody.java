package com.openwave.mms.mm7.soap;

public class SOAPBody {
    private SOAPMethod method;

    public SOAPBody() {}

    public void setMethod( SOAPMethod method ) {
        this.method = method;
    }

    public SOAPMethod getMethod() { return method; }

    public void serialize( StringBuffer buffer ) {
        buffer.append( "<" );
        buffer.append( SOAPConsts.SOAPEnvPrefix );
        buffer.append( ":" );
        buffer.append( SOAPConsts.SOAPBody );
        buffer.append( ">" );

        if( method != null ) {
            method.serialize( buffer );
        }

        buffer.append( "</" );
        buffer.append( SOAPConsts.SOAPEnvPrefix );
        buffer.append( ":" );
        buffer.append( SOAPConsts.SOAPBody );
        buffer.append( ">" );
    }
}
