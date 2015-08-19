// Decompiled by Jad v1.5.8g. Copyright 2001 Pavel Kouznetsov.
// Jad home page: http://www.kpdus.com/jad.html
// Decompiler options: packimports(3) 
// Source File Name:   LayoutTypeDescriptor.java

package com.openwave.mms.content.smil;

import org.exolab.castor.mapping.*;
import org.exolab.castor.xml.*;
import org.exolab.castor.xml.util.XMLClassDescriptorImpl;
import org.exolab.castor.xml.util.XMLFieldDescriptorImpl;

// Referenced classes of package com.openwave.mms.content.smil:
//            LayoutType, RootLayout, Region

public class LayoutTypeDescriptor extends XMLClassDescriptorImpl
{

    public LayoutTypeDescriptor()
    {
        xmlName = "layoutType";
        XMLFieldDescriptorImpl desc = null;
        XMLFieldHandler handler = null;
        FieldValidator fieldValidator = null;
        setCompositorAsSequence();
        desc = new XMLFieldDescriptorImpl(com.openwave.mms.content.smil.RootLayout.class, "_rootLayout", "root-layout", NodeType.Element);
        handler = new XMLFieldHandler() {

            public Object getValue(Object object)
                throws IllegalStateException
            {
                LayoutType target = (LayoutType)object;
                return target.getRootLayout();
            }

            public void setValue(Object object, Object value)
                throws IllegalStateException, IllegalArgumentException
            {
                try
                {
                    LayoutType target = (LayoutType)object;
                    target.setRootLayout((RootLayout)value);
                }
                catch(Exception ex)
                {
                    throw new IllegalStateException(ex.toString());
                }
            }

            public Object newInstance(Object parent)
            {
                return new RootLayout();
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
        desc = new XMLFieldDescriptorImpl(com.openwave.mms.content.smil.Region.class, "_regionList", "region", NodeType.Element);
        handler = new XMLFieldHandler() {

            public Object getValue(Object object)
                throws IllegalStateException
            {
                LayoutType target = (LayoutType)object;
                return target.getRegion();
            }

            public void setValue(Object object, Object value)
                throws IllegalStateException, IllegalArgumentException
            {
                try
                {
                    LayoutType target = (LayoutType)object;
                    target.addRegion((Region)value);
                }
                catch(Exception ex)
                {
                    throw new IllegalStateException(ex.toString());
                }
            }

            public Object newInstance(Object parent)
            {
                return new Region();
            }

        }
;
        desc.setHandler(handler);
        desc.setRequired(true);
        desc.setMultivalued(true);
        addFieldDescriptor(desc);
        fieldValidator = new FieldValidator();
        fieldValidator.setMinOccurs(0);
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
        return com.openwave.mms.content.smil.LayoutType.class;
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
