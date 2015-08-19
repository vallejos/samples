// Decompiled by Jad v1.5.8g. Copyright 2001 Pavel Kouznetsov.
// Jad home page: http://www.kpdus.com/jad.html
// Decompiler options: packimports(3) 
// Source File Name:   VideoType.java

package com.openwave.mms.content.smil;

import java.io.*;
import org.exolab.castor.xml.*;
import org.xml.sax.ContentHandler;

public abstract class VideoType
    implements Serializable
{

    public VideoType()
    {
    }

    public String getAlt()
    {
        return _alt;
    }

    public String getBegin()
    {
        return _begin;
    }

    public String getEnd()
    {
        return _end;
    }

    public String getRegion()
    {
        return _region;
    }

    public String getSrc()
    {
        return _src;
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

    public void setAlt(String alt)
    {
        _alt = alt;
    }

    public void setBegin(String begin)
    {
        _begin = begin;
    }

    public void setEnd(String end)
    {
        _end = end;
    }

    public void setRegion(String region)
    {
        _region = region;
    }

    public void setSrc(String src)
    {
        _src = src;
    }

    public void validate()
        throws ValidationException
    {
        Validator validator = new Validator();
        validator.validate(this);
    }

    private String _src;
    private String _region;
    private String _alt;
    private String _begin;
    private String _end;
}
