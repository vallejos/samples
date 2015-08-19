// Decompiled by Jad v1.5.8g. Copyright 2001 Pavel Kouznetsov.
// Jad home page: http://www.kpdus.com/jad.html
// Decompiler options: packimports(3) 
// Source File Name:   LayoutDescriptor.java

package com.openwave.mms.content.smil;

import org.exolab.castor.mapping.*;
import org.exolab.castor.xml.TypeValidator;
import org.exolab.castor.xml.XMLFieldDescriptor;

// Referenced classes of package com.openwave.mms.content.smil:
//            LayoutTypeDescriptor

public class LayoutDescriptor extends LayoutTypeDescriptor
{

    public LayoutDescriptor()
    {
        setExtendsWithoutFlatten(new LayoutTypeDescriptor());
        xmlName = "layout";
        org.exolab.castor.xml.util.XMLFieldDescriptorImpl desc = null;
        org.exolab.castor.xml.XMLFieldHandler handler = null;
        org.exolab.castor.xml.FieldValidator fieldValidator = null;
    }

    public AccessMode getAccessMode()
    {
        return null;
    }

    public ClassDescriptor getExtends()
    {
        return super.getExtends();
    }

    public FieldDescriptor getIdentity()
    {
        if(identity == null)
            return super.getIdentity();
        else
            return identity;
    }

    public Class getJavaClass()
    {
        return com.openwave.mms.content.smil.Layout.class;
    }

    public String getNameSpacePrefix()
    {
        return nsPrefix;
    }

    public String getNameSpaceURI()
    {
        return nsURI;
    }

    public TypeValidator getValidator()
    {
        return this;
    }

    public String getXMLName()
    {
        return xmlName;
    }

    private String nsPrefix;
    private String nsURI;
    private String xmlName;
    private XMLFieldDescriptor identity;
}
