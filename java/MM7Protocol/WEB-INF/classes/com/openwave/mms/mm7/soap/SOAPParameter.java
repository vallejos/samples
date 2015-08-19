package com.openwave.mms.mm7.soap;

import java.util.Iterator;
import java.util.HashMap;
import java.util.Map;
import java.util.Vector;

public class SOAPParameter {

    private SOAPQName qName;
    private String value;
    protected Vector parameters;
    protected HashMap attributes;
    private boolean leaf;

    public SOAPParameter( String name ) throws SOAPException {
        if( name == null || name.length() == 0 )
            throw new SOAPException( "parameter-name-cannot-be-null" );
        qName = new SOAPQName( name );
        leaf = true;
    }

    public SOAPParameter( SOAPQName name ) throws SOAPException {
        if( name == null )
            throw new SOAPException( "parameter-name-cannot-be-null" );
        qName = name;
        leaf = true;
    }

    public SOAPParameter( SOAPQName name,
                          HashMap attributes,
                          String value ) throws SOAPException {
        if( name == null )
            throw new SOAPException( "parameter-name-cannot-be-null" );
        qName = name;
        this.value = value;
        this.attributes = attributes;
        leaf = true;
    }

    public SOAPParameter( SOAPQName name,
                          HashMap attributes,
                          Vector parameters ) throws SOAPException {
        if( name == null )
            throw new SOAPException( "parameter-name-cannot-be-null" );
        qName = name;
        this.attributes = attributes;
        this.parameters = parameters;
        leaf = false;
    }

    public String getName() {
        return qName.getName();
    }

    public String getNamespace() {
        return qName.getUri();
    }

    public String getNamespacePrefix() {
        return qName.getPrefix();
    }

    public Vector getParameters( ) throws SOAPException {
        if( !leaf ) return parameters;
        else throw new SOAPException( "cannot-get-parameters-of-leaf-node" );
    }

    public String getValue() throws SOAPException {
        if( leaf ) return value;
        else throw new SOAPException( "cannot-get-value-of-inner-node" );
    }

    public String getValue( String paramName ) throws SOAPException {
        if( parameters == null ) return null;
        for( int i = 0; i < parameters.size(); i++ ) {
            SOAPParameter param = (SOAPParameter) parameters.get( i );
            if( param.qName.getName().equalsIgnoreCase( paramName ) ) {
                if( param.leaf ) return param.value;
                else throw new SOAPException( "cannot-get-value-of-inner-node" );
            }
        }
        return null;
    }

    public SOAPParameter getParameter( String paramName ) throws SOAPException {
        if( parameters == null ) return null;
        for( int i = 0; i < parameters.size(); i++ ) {
            SOAPParameter param = (SOAPParameter) parameters.get( i );
            if( param.qName.getName().equalsIgnoreCase( paramName ) ) {
                if( !param.leaf ) return param;
                else throw new SOAPException( "cannot-get-parameters-of-leaf-node" );
            }
        }
        return null;
    }

    public void setValue( String value ) throws SOAPException {
        if( parameters == null ) {
            this.value = value;
            leaf = true;
        } else throw new SOAPException( "cannot-set-value-to-inner-node" );
    }

    public void addParameter( SOAPParameter param ) throws SOAPException {
        if( value != null )
            throw new SOAPException( "cannot-add-parameter-to-leaf-node" );
        if( parameters == null ) {
            parameters = new Vector();
        }
        parameters.add( param );
        leaf = false;
    }

    public void addParameter( String name,
                              String value ) throws SOAPException {
        SOAPParameter param = new SOAPParameter( name );
        param.setValue( value );
        addParameter( param );
    }

    public SOAPParameter addParameter( String name ) throws SOAPException {
        SOAPParameter param = new SOAPParameter( name );
        addParameter( param );
        return param;
    }

    public void addAttribute( String name, String value ) {
        if( attributes == null ) {
            attributes = new HashMap();
        }
        attributes.put( new SOAPQName( name ), value );
    }


    public void serialize( StringBuffer buffer ) {
        buffer.append( "<" );
        String prefix = qName.getPrefix();
        if( prefix != null && prefix.length() > 0 ) {
            buffer.append( prefix );
            buffer.append( ":" );
        }
        buffer.append( qName.getName() );
        String uri = qName.getUri();
        if( uri != null && uri.length() > 0 ) {
            buffer.append( " xmlns" );
            if( prefix != null && prefix.length() > 0 ) {
                buffer.append( ":" );
                buffer.append( prefix );
            }
            buffer.append( "=\"" );
            buffer.append( uri );
            buffer.append( "\"" );
        }
        if( attributes != null ) {
            Iterator iter = attributes.entrySet().iterator();
            while( iter.hasNext() ) {
                Map.Entry entry = ( Map.Entry ) iter.next();
                SOAPQName attrName = ( SOAPQName ) entry.getKey();
                prefix = attrName.getPrefix();
                buffer.append( " " );
                if( prefix != null && prefix.length() > 0 ) {
                    buffer.append( prefix );
                    buffer.append( ":" );
                }
                buffer.append( attrName.getName() );
                buffer.append( "=\"" );
                buffer.append( ( String ) entry.getValue() );
                buffer.append( "\"" );
            }
        }
        if( leaf ) {
            if( value != null && value.length() > 0 ) {
                buffer.append( ">" );
                buffer.append( value );
                buffer.append( "</" );
                prefix = qName.getPrefix();
                if( prefix != null && prefix.length() > 0 ) {
                    buffer.append( prefix );
                    buffer.append( ":" );
                }
                buffer.append( qName.getName() );
                buffer.append( ">" );
            } else {
                buffer.append( " />" );
            }
        } else {
            buffer.append( ">" );
            if( parameters != null ) {
                for( int i = 0; i < parameters.size(); i++ ) {
                    SOAPParameter param = ( SOAPParameter ) parameters.get( i );
                    param.serialize( buffer );
                }
            }
            buffer.append( "</" );
            prefix = qName.getPrefix();
            if( prefix != null && prefix.length() > 0 ) {
                buffer.append( prefix );
                buffer.append( ":" );
            }
            buffer.append( qName.getName() );
            buffer.append( ">" );
        }
    }

}
