// Decompiled by Jad v1.5.8g. Copyright 2001 Pavel Kouznetsov.
// Jad home page: http://www.kpdus.com/jad.html
// Decompiler options: packimports(3) 
// Source File Name:   RootLayoutTypeDescriptor.java

package com.openwave.mms.content.smil;

import org.exolab.castor.mapping.*;
import org.exolab.castor.xml.*;
import org.exolab.castor.xml.util.XMLClassDescriptorImpl;
import org.exolab.castor.xml.util.XMLFieldDescriptorImpl;
import org.exolab.castor.xml.validators.StringValidator;

// Referenced classes of package com.openwave.mms.content.smil:
//            RootLayoutType

public class RootLayoutTypeDescriptor extends XMLClassDescriptorImpl
{

    public RootLayoutTypeDescriptor()
    {
        xmlName = "root-layoutType";
        XMLFieldDescriptorImpl desc = null;
        XMLFieldHandler handler = null;
        FieldValidator fieldValidator = null;
        desc = new XMLFieldDescriptorImpl(java.lang.String.class, "_backgroundColor", "background-color", NodeType.Attribute);
        desc.setImmutable(true);
        handler = new XMLFieldHandler() {

            public Object getValue(Object object)
                throws IllegalStateException
            {
                RootLayoutType target = (RootLayoutType)object;
                return target.getBackgroundColor();
            }

            public void setValue(Object object, Object value)
                throws IllegalStateException, IllegalArgumentException
            {
                try
                {
                    RootLayoutType target = (RootLayoutType)object;
                    target.setBackgroundColor((String)value);
                }
                catch(Exception ex)
                {
                    throw new IllegalStateException(ex.toString());
                }
            }

            public Object newInstance(Object parent)
            {
                return null;
            }

        }
;
        desc.setHandler(handler);
        addFieldDescriptor(desc);
        fieldValidator = new FieldValidator();
        StringValidator sv = new StringValidator();
        sv.setWhiteSpace("preserve");
        fieldValidator.setValidator(sv);
        desc.setValidator(fieldValidator);
        desc = new XMLFieldDescriptorImpl(java.lang.String.class, "_width", "width", NodeType.Attribute);
        desc.setImmutable(true);
        handler = new XMLFieldHandler() {

            public Object getValue(Object object)
                throws IllegalStateException
            {
                RootLayoutType target = (RootLayoutType)object;
                return target.getWidth();
            }

            public void setValue(Object object, Object value)
                throws IllegalStateException, IllegalArgumentException
            {
                try
                {
                    RootLayoutType target = (RootLayoutType)object;
                    target.setWidth((String)value);
                }
                catch(Exception ex)
                {
                    throw new IllegalStateException(ex.toString());
                }
            }

            public Object newInstance(Object parent)
            {
                return null;
            }

        }
;
        desc.setHandler(handler);
        addFieldDescriptor(desc);
        fieldValidator = new FieldValidator();
        sv = new StringValidator();
        sv.setWhiteSpace("preserve");
        fieldValidator.setValidator(sv);
        desc.setValidator(fieldValidator);
        desc = new XMLFieldDescriptorImpl(java.lang.String.class, "_height", "height", NodeType.Attribute);
        desc.setImmutable(true);
        handler = new XMLFieldHandler() {

            public Object getValue(Object object)
                throws IllegalStateException
            {
                RootLayoutType target = (RootLayoutType)object;
                return target.getHeight();
            }

            public void setValue(Object object, Object value)
                throws IllegalStateException, IllegalArgumentException
            {
                try
                {
                    RootLayoutType target = (RootLayoutType)object;
                    target.setHeight((String)value);
                }
                catch(Exception ex)
                {
                    throw new IllegalStateException(ex.toString());
                }
            }

            public Object newInstance(Object parent)
            {
                return null;
            }

        }
;
        desc.setHandler(handler);
        addFieldDescriptor(desc);
        fieldValidator = new FieldValidator();
        sv = new StringValidator();
        sv.setWhiteSpace("preserve");
        fieldValidator.setValidator(sv);
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
        return com.openwave.mms.content.smil.RootLayoutType.class;
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
