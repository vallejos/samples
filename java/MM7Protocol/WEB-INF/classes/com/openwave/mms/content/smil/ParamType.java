// Decompiled by Jad v1.5.8g. Copyright 2001 Pavel Kouznetsov.
// Jad home page: http://www.kpdus.com/jad.html
// Decompiler options: packimports(3) 
// Source File Name:   ParamType.java

package com.openwave.mms.content.smil;

import java.io.*;
import org.exolab.castor.xml.*;
import org.xml.sax.ContentHandler;

public abstract class ParamType
    implements Serializable
{

    public ParamType()
    {
    }

    public String getName()
    {
        return _name;
    }

    public String getValue()
    {
        return _value;
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

    public abstract void marshal(Writer writer)
        throws MarshalException, ValidationException;

    public abstract void marshal(ContentHandler contenthandler)
        throws IOException, MarshalException, ValidationException;

    public void setName(String name)
    {
        _name = name;
    }

    public void setValue(String value)
    {
        _value = value;
    }

    public void validate()
        throws ValidationException
    {
        Validator validator = new Validator();
        validator.validate(this);
    }

    private String _name;
    private String _value;
}
