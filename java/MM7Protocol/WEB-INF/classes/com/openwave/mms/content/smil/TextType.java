// Decompiled by Jad v1.5.8g. Copyright 2001 Pavel Kouznetsov.
// Jad home page: http://www.kpdus.com/jad.html
// Decompiler options: packimports(3) 
// Source File Name:   TextType.java

package com.openwave.mms.content.smil;

import java.io.*;
import java.util.ArrayList;
import java.util.Enumeration;
import org.exolab.castor.util.IteratorEnumeration;
import org.exolab.castor.xml.*;
import org.xml.sax.ContentHandler;

// Referenced classes of package com.openwave.mms.content.smil:
//            Param

public abstract class TextType
    implements Serializable
{

    public TextType()
    {
        _paramList = new ArrayList();
    }

    public void addParam(Param vParam)
        throws IndexOutOfBoundsException
    {
        _paramList.add(vParam);
    }

    public void addParam(int index, Param vParam)
        throws IndexOutOfBoundsException
    {
        _paramList.add(index, vParam);
    }

    public void clearParam()
    {
        _paramList.clear();
    }

    public Enumeration enumerateParam()
    {
        return new IteratorEnumeration(_paramList.iterator());
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

    public Param getParam(int index)
        throws IndexOutOfBoundsException
    {
        if(index < 0 || index > _paramList.size())
            throw new IndexOutOfBoundsException();
        else
            return (Param)_paramList.get(index);
    }

    public Param[] getParam()
    {
        int size = _paramList.size();
        Param mArray[] = new Param[size];
        for(int index = 0; index < size; index++)
            mArray[index] = (Param)_paramList.get(index);

        return mArray;
    }

    public int getParamCount()
    {
        return _paramList.size();
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

    public boolean removeParam(Param vParam)
    {
        boolean removed = _paramList.remove(vParam);
        return removed;
    }

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

    public void setParam(int index, Param vParam)
        throws IndexOutOfBoundsException
    {
        if(index < 0 || index > _paramList.size())
        {
            throw new IndexOutOfBoundsException();
        } else
        {
            _paramList.set(index, vParam);
            return;
        }
    }

    public void setParam(Param paramArray[])
    {
        _paramList.clear();
        for(int i = 0; i < paramArray.length; i++)
            _paramList.add(paramArray[i]);

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
    private ArrayList _paramList;
}
