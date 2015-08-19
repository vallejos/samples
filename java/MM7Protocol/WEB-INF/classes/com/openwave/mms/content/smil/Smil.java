// Decompiled by Jad v1.5.8g. Copyright 2001 Pavel Kouznetsov.
// Jad home page: http://www.kpdus.com/jad.html
// Decompiler options: packimports(3) 
// Source File Name:   Smil.java

package com.openwave.mms.content.smil;

import java.io.*;
import org.exolab.castor.xml.*;
import org.xml.sax.ContentHandler;

// Referenced classes of package com.openwave.mms.content.smil:
//            Head, Body

public class Smil
    implements Serializable
{

    public Smil()
    {
    }

    public Body getBody()
    {
        return _body;
    }

    public Head getHead()
    {
        return _head;
    }

    public String getXmlns()
    {
        return _xmlns;
    }

    public boolean isValid()
    {
        try
        {
            validate();
        }
        catch(ValidationException vex)
        {
            return false;
        }
        return true;
    }

    public void marshal(Writer out)
        throws MarshalException, ValidationException
    {
        Marshaller.marshal(this, out);
    }

    public void marshal(ContentHandler handler)
        throws IOException, MarshalException, ValidationException
    {
        Marshaller.marshal(this, handler);
    }

    public void setBody(Body body)
    {
        _body = body;
    }

    public void setHead(Head head)
    {
        _head = head;
    }

    public void setXmlns(String xmlns)
    {
        _xmlns = xmlns;
    }

    public static Smil unmarshal(Reader reader)
        throws MarshalException, ValidationException
    {
        return (Smil)Unmarshaller.unmarshal(com.openwave.mms.content.smil.Smil.class, reader);
    }

    public void validate()
        throws ValidationException
    {
        Validator validator = new Validator();
        validator.validate(this);
    }

    private String _xmlns;
    private Head _head;
    private Body _body;
}
