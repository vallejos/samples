// Decompiled by Jad v1.5.8g. Copyright 2001 Pavel Kouznetsov.
// Jad home page: http://www.kpdus.com/jad.html
// Decompiler options: packimports(3) 
// Source File Name:   HeadTypeDescriptor.java

package com.openwave.mms.content.smil;

import org.exolab.castor.mapping.*;
import org.exolab.castor.xml.*;
import org.exolab.castor.xml.util.XMLClassDescriptorImpl;
import org.exolab.castor.xml.util.XMLFieldDescriptorImpl;

// Referenced classes of package com.openwave.mms.content.smil:
//            HeadType, Meta, Layout

public class HeadTypeDescriptor extends XMLClassDescriptorImpl
{

    public HeadTypeDescriptor()
    {
        xmlName = "headType";
        XMLFieldDescriptorImpl desc = null;
        XMLFieldHandler handler = null;
        FieldValidator fieldValidator = null;
        setCompositorAsSequence();
        desc = new XMLFieldDescriptorImpl(com.openwave.mms.content.smil.Meta.class, "_metaList", "meta", NodeType.Element);
        handler = new XMLFieldHandler() {

            public Object getValue(Object object)
                throws IllegalStateException
            {
                HeadType target = (HeadType)object;
                return target.getMeta();
            }

            public void setValue(Object object, Object value)
                throws IllegalStateException, IllegalArgumentException
            {
                try
                {
                    HeadType target = (HeadType)object;
                    target.addMeta((Meta)value);
                }
                catch(Exception ex)
                {
                    throw new IllegalStateException(ex.toString());
                }
            }

            public Object newInstance(Object parent)
            {
                return new Meta();
            }

        }
;
        desc.setHandler(handler);
        desc.setMultivalued(true);
        addFieldDescriptor(desc);
        fieldValidator = new FieldValidator();
        fieldValidator.setMinOccurs(0);
        desc.setValidator(fieldValidator);
        desc = new XMLFieldDescriptorImpl(com.openwave.mms.content.smil.Layout.class, "_layout", "layout", NodeType.Element);
        handler = new XMLFieldHandler() {

            public Object getValue(Object object)
                throws IllegalStateException
            {
                HeadType target = (HeadType)object;
                return target.getLayout();
            }

            public void setValue(Object object, Object value)
                throws IllegalStateException, IllegalArgumentException
            {
                try
                {
                    HeadType target = (HeadType)object;
                    target.setLayout((Layout)value);
                }
                catch(Exception ex)
                {
                    throw new IllegalStateException(ex.toString());
                }
            }

            public Object newInstance(Object parent)
            {
                return new Layout();
            }

        }
;
        desc.setHandler(handler);
        desc.setRequired(true);
        desc.setMultivalued(false);
        addFieldDescriptor(desc);
        fieldValidator = new FieldValidator();
        fieldValidator.setMinOccurs(1);
        desc.setValidator(fieldValidator);
    }

    public AccessMode getAccessMode()
    {
        return null;
    }

    public ClassDescriptor getExtends()
    {
        return null;
    }

    public FieldDescriptor getIdentity()
    {
        return identity;
    }

    public Class getJavaClass()
    {
        return com.openwave.mms.content.smil.HeadType.class;
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
