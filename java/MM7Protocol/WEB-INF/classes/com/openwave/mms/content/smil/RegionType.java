// Decompiled by Jad v1.5.8g. Copyright 2001 Pavel Kouznetsov.
// Jad home page: http://www.kpdus.com/jad.html
// Decompiler options: packimports(3) 
// Source File Name:   RegionType.java

package com.openwave.mms.content.smil;

import com.openwave.mms.content.smil.types.RegionTypeIdType;
import java.io.*;
import org.exolab.castor.xml.*;
import org.xml.sax.ContentHandler;

public abstract class RegionType
    implements Serializable
{

    public RegionType()
    {
    }

    public String getFit()
    {
        return _fit;
    }

    public String getHeight()
    {
        return _height;
    }

    public RegionTypeIdType getId()
    {
        return _id;
    }

    public String getLeft()
    {
        return _left;
    }

    public String getTop()
    {
        return _top;
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

    public void setFit(String fit)
    {
        _fit = fit;
    }

    public void setHeight(String height)
    {
        _height = height;
    }

    public void setId(RegionTypeIdType id)
    {
        _id = id;
    }

    public void setLeft(String left)
    {
        _left = left;
    }

    public void setTop(String top)
    {
        _top = top;
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

    private RegionTypeIdType _id;
    private String _top;
    private String _left;
    private String _height;
    private String _width;
    private String _fit;
}
