// Decompiled by Jad v1.5.8g. Copyright 2001 Pavel Kouznetsov.
// Jad home page: http://www.kpdus.com/jad.html
// Decompiler options: packimports(3) 
// Source File Name:   BodyType.java

package com.openwave.mms.content.smil;

import java.io.*;
import java.util.ArrayList;
import java.util.Enumeration;
import org.exolab.castor.util.IteratorEnumeration;
import org.exolab.castor.xml.*;
import org.xml.sax.ContentHandler;

// Referenced classes of package com.openwave.mms.content.smil:
//            Par

public abstract class BodyType
    implements Serializable
{

    public BodyType()
    {
        _parList = new ArrayList();
    }

    public void addPar(Par vPar)
        throws IndexOutOfBoundsException
    {
        _parList.add(vPar);
    }

    public void addPar(int index, Par vPar)
        throws IndexOutOfBoundsException
    {
        _parList.add(index, vPar);
    }

    public void clearPar()
    {
        _parList.clear();
    }

    public Enumeration enumeratePar()
    {
        return new IteratorEnumeration(_parList.iterator());
    }

    public Par getPar(int index)
        throws IndexOutOfBoundsException
    {
        if(index < 0 || index > _parList.size())
            throw new IndexOutOfBoundsException();
        else
            return (Par)_parList.get(index);
    }

    public Par[] getPar()
    {
        int size = _parList.size();
        Par mArray[] = new Par[size];
        for(int index = 0; index < size; index++)
            mArray[index] = (Par)_parList.get(index);

        return mArray;
    }

    public int getParCount()
    {
        return _parList.size();
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

    public boolean removePar(Par vPar)
    {
        boolean removed = _parList.remove(vPar);
        return removed;
    }

    public void setPar(int index, Par vPar)
        throws IndexOutOfBoundsException
    {
        if(index < 0 || index > _parList.size())
        {
            throw new IndexOutOfBoundsException();
        } else
        {
            _parList.set(index, vPar);
            return;
        }
    }

    public void setPar(Par parArray[])
    {
        _parList.clear();
        for(int i = 0; i < parArray.length; i++)
            _parList.add(parArray[i]);

    }

    public void validate()
        throws ValidationException
    {
        Validator validator = new Validator();
        validator.validate(this);
    }

    private ArrayList _parList;
}
