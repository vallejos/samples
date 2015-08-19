// Decompiled by Jad v1.5.8g. Copyright 2001 Pavel Kouznetsov.
// Jad home page: http://www.kpdus.com/jad.html
// Decompiler options: packimports(3) 
// Source File Name:   Par.java

package com.openwave.mms.content.smil;

import java.io.*;
import org.exolab.castor.xml.*;
import org.xml.sax.ContentHandler;

// Referenced classes of package com.openwave.mms.content.smil:
//            ParType

public class Par extends ParType
    implements Serializable
{

    public Par()
    {
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

    public static Par unmarshal(Reader reader)
        throws MarshalException, ValidationException
    {
        return (Par)Unmarshaller.unmarshal(com.openwave.mms.content.smil.Par.class, reader);
    }

    public void validate()
        throws ValidationException
    {
        Validator validator = new Validator();
        validator.validate(this);
    }
}
