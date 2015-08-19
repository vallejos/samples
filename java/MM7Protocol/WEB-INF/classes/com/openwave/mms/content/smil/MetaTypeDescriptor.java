// Decompiled by Jad v1.5.8g. Copyright 2001 Pavel Kouznetsov.
// Jad home page: http://www.kpdus.com/jad.html
// Decompiler options: packimports(3) 
// Source File Name:   MetaTypeDescriptor.java

package com.openwave.mms.content.smil;

import org.exolab.castor.mapping.*;
import org.exolab.castor.xml.*;
import org.exolab.castor.xml.util.XMLClassDescriptorImpl;
import org.exolab.castor.xml.util.XMLFieldDescriptorImpl;
import org.exolab.castor.xml.validators.StringValidator;

// Referenced classes of package com.openwave.mms.content.smil:
//            MetaType

public class MetaTypeDescriptor extends XMLClassDescriptorImpl
{

    public MetaTypeDescriptor()
    {
        xmlName = "metaType";
        XMLFieldDescriptorImpl desc = null;
        XMLFieldHandler handler = null;
        FieldValidator fieldValidator = null;
        desc = new XMLFieldDescriptorImpl(java.lang.String.class, "_name", "name", NodeType.Attribute);
        desc.setImmutable(true);
        handler = new XMLFieldHandler() {

            public Object getValue(Object object)
                throws IllegalStateException
            {
                MetaType target = (MetaType)object;
                return target.getName();
            }

            public void setValue(Object object, Object value)
                throws IllegalStateException, IllegalArgumentException
            {
                try
                {
                    MetaType target = (MetaType)object;
                    target.setName((String)value);
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
        desc.setRequired(true);
        addFieldDescriptor(desc);
        fieldValidator = new FieldValidator();
        fieldValidator.setMinOccurs(1);
        StringValidator sv = new StringValidator();
        sv.setWhiteSpace("preserve");
        fieldValidator.setValidator(sv);
        desc.setValidator(fieldValidator);
        desc = new XMLFieldDescriptorImpl(java.lang.String.class, "_content", "content", NodeType.Attribute);
        desc.setImmutable(true);
        handler = new XMLFieldHandler() {

            public Object getValue(Object object)
                throws IllegalStateException
            {
                MetaType target = (MetaType)object;
                return target.getContent();
            }

            public void setValue(Object object, Object value)
                throws IllegalStateException, IllegalArgumentException
            {
                try
                {
                    MetaType target = (MetaType)object;
                    target.setContent((String)value);
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
        desc.setRequired(true);
        addFieldDescriptor(desc);
        fieldValidator = new FieldValidator();
        fieldValidator.setMinOccurs(1);
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
        return com.openwave.mms.content.smil.MetaType.class;
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
