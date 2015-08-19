// Decompiled by Jad v1.5.8g. Copyright 2001 Pavel Kouznetsov.
// Jad home page: http://www.kpdus.com/jad.html
// Decompiler options: packimports(3) 
// Source File Name:   LayoutType.java

package com.openwave.mms.content.smil;

import java.io.*;
import java.util.ArrayList;
import java.util.Enumeration;
import org.exolab.castor.util.IteratorEnumeration;
import org.exolab.castor.xml.*;
import org.xml.sax.ContentHandler;

// Referenced classes of package com.openwave.mms.content.smil:
//            Region, RootLayout

public abstract class LayoutType
    implements Serializable
{

    public LayoutType()
    {
        _regionList = new ArrayList();
    }

    public void addRegion(Region vRegion)
        throws IndexOutOfBoundsException
    {
        _regionList.add(vRegion);
    }

    public void addRegion(int index, Region vRegion)
        throws IndexOutOfBoundsException
    {
        _regionList.add(index, vRegion);
    }

    public void clearRegion()
    {
        _regionList.clear();
    }

    public Enumeration enumerateRegion()
    {
        return new IteratorEnumeration(_regionList.iterator());
    }

    public Region getRegion(int index)
        throws IndexOutOfBoundsException
    {
        if(index < 0 || index > _regionList.size())
            throw new IndexOutOfBoundsException();
        else
            return (Region)_regionList.get(index);
    }

    public Region[] getRegion()
    {
        int size = _regionList.size();
        Region mArray[] = new Region[size];
        for(int index = 0; index < size; index++)
            mArray[index] = (Region)_regionList.get(index);

        return mArray;
    }

    public int getRegionCount()
    {
        return _regionList.size();
    }

    public RootLayout getRootLayout()
    {
        return _rootLayout;
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

    public boolean removeRegion(Region vRegion)
    {
        boolean removed = _regionList.remove(vRegion);
        return removed;
    }

    public void setRegion(int index, Region vRegion)
        throws IndexOutOfBoundsException
    {
        if(index < 0 || index > _regionList.size())
        {
            throw new IndexOutOfBoundsException();
        } else
        {
            _regionList.set(index, vRegion);
            return;
        }
    }

    public void setRegion(Region regionArray[])
    {
        _regionList.clear();
        for(int i = 0; i < regionArray.length; i++)
            _regionList.add(regionArray[i]);

    }

    public void setRootLayout(RootLayout rootLayout)
    {
        _rootLayout = rootLayout;
    }

    public void validate()
        throws ValidationException
    {
        Validator validator = new Validator();
        validator.validate(this);
    }

    private RootLayout _rootLayout;
    private ArrayList _regionList;
}
