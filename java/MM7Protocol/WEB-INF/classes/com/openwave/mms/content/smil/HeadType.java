// Decompiled by Jad v1.5.8g. Copyright 2001 Pavel Kouznetsov.
// Jad home page: http://www.kpdus.com/jad.html
// Decompiler options: packimports(3) 
// Source File Name:   HeadType.java

package com.openwave.mms.content.smil;

import java.io.*;
import java.util.ArrayList;
import java.util.Enumeration;
import org.exolab.castor.util.IteratorEnumeration;
import org.exolab.castor.xml.*;
import org.xml.sax.ContentHandler;

// Referenced classes of package com.openwave.mms.content.smil:
//            Meta, Layout

public abstract class HeadType
    implements Serializable
{

    public HeadType()
    {
        _metaList = new ArrayList();
    }

    public void addMeta(Meta vMeta)
        throws IndexOutOfBoundsException
    {
        _metaList.add(vMeta);
    }

    public void addMeta(int index, Meta vMeta)
        throws IndexOutOfBoundsException
    {
        _metaList.add(index, vMeta);
    }

    public void clearMeta()
    {
        _metaList.clear();
    }

    public Enumeration enumerateMeta()
    {
        return new IteratorEnumeration(_metaList.iterator());
    }

    public Layout getLayout()
    {
        return _layout;
    }

    public Meta getMeta(int index)
        throws IndexOutOfBoundsException
    {
        if(index < 0 || index > _metaList.size())
            throw new IndexOutOfBoundsException();
        else
            return (Meta)_metaList.get(index);
    }

    public Meta[] getMeta()
    {
        int size = _metaList.size();
        Meta mArray[] = new Meta[size];
        for(int index = 0; index < size; index++)
            mArray[index] = (Meta)_metaList.get(index);

        return mArray;
    }

    public int getMetaCount()
    {
        return _metaList.size();
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

    public boolean removeMeta(Meta vMeta)
    {
        boolean removed = _metaList.remove(vMeta);
        return removed;
    }

    public void setLayout(Layout layout)
    {
        _layout = layout;
    }

    public void setMeta(int index, Meta vMeta)
        throws IndexOutOfBoundsException
    {
        if(index < 0 || index > _metaList.size())
        {
            throw new IndexOutOfBoundsException();
        } else
        {
            _metaList.set(index, vMeta);
            return;
        }
    }

    public void setMeta(Meta metaArray[])
    {
        _metaList.clear();
        for(int i = 0; i < metaArray.length; i++)
            _metaList.add(metaArray[i]);

    }

    public void validate()
        throws ValidationException
    {
        Validator validator = new Validator();
        validator.validate(this);
    }

    private ArrayList _metaList;
    private Layout _layout;
}
