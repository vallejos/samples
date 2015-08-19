package com.openwave.mms.mm7.soap;

import java.util.Vector;

public class SOAPMethod extends SOAPParameter {

    public SOAPMethod( SOAPQName name,
                       Vector params )
                       throws SOAPException {
        super( name, null, params );
    }

    public SOAPMethod( String name )
                       throws SOAPException {
        this( new SOAPQName( name, "mm7", SOAPConsts.MM7Namespaces[3] ),
             ( Vector ) null );
    }

    public void serialize( StringBuffer buffer ) {
         buffer.append( "<" + getName() + " xmlns=\"" + getNamespace() + "\"" + ">" );
         if( parameters != null ) {
             for( int i = 0; i < parameters.size(); i++ ) {
                 SOAPParameter param = ( SOAPParameter ) parameters.get( i );
                 param.serialize( buffer );
             }
         }
         buffer.append( "</" + getName() + ">" );
    }

}
