// Decompiled by Jad v1.5.8g. Copyright 2001 Pavel Kouznetsov.
// Jad home page: http://www.kpdus.com/jad.html
// Decompiler options: packimports(3) 
// Source File Name:   RootLayoutType.java

package com.openwave.mms.content.smil;

import java.io.*;
import org.exolab.castor.xml.*;
import org.xml.sax.ContentHandler;

public abstract class RootLayoutType
    implements Serializable
{

    public RootLayoutType()
    {
    }

    public String getBackgroundColor()
    {
        return _backgroundColor;
    }

    public String getHeight()
    {
        return _height;
    }

    public String getWidth()
    {
        return _width;
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

    public void setBackgroundColor(String backgroundColor)
    {
        _backgroundColor = backgroundColor;
    }

    public void setHeight(String height)
    {
        _height = height;
    }

    public void setWidth(String width)
    {
        _width = width;
    }

    public void validate()
        throws ValidationException
    {
        Validator validator = new Validator();
        validator.validate(this);
    }

    private String _backgroundColor;
    private String _width;
    private String _height;
}
