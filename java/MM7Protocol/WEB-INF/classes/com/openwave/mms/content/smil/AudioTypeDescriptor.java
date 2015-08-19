// Decompiled by Jad v1.5.8g. Copyright 2001 Pavel Kouznetsov.
// Jad home page: http://www.kpdus.com/jad.html
// Decompiler options: packimports(3) 
// Source File Name:   AudioTypeDescriptor.java

package com.openwave.mms.content.smil;

import org.exolab.castor.mapping.*;
import org.exolab.castor.xml.*;
import org.exolab.castor.xml.util.XMLClassDescriptorImpl;
import org.exolab.castor.xml.util.XMLFieldDescriptorImpl;
import org.exolab.castor.xml.validators.StringValidator;

// Referenced classes of package com.openwave.mms.content.smil:
//            AudioType

public class AudioTypeDescriptor extends XMLClassDescriptorImpl
{

    public AudioTypeDescriptor()
    {
        xmlName = "audioType";
        XMLFieldDescriptorImpl desc = null;
        XMLFieldHandler handler = null;
        FieldValidator fieldValidator = null;
        desc = new XMLFieldDescriptorImpl(java.lang.String.class, "_src", "src", NodeType.Attribute);
        desc.setImmutable(true);
        handler = new XMLFieldHandler() {

            public Object getValue(Object object)
                throws IllegalStateException
            {
                AudioType target = (AudioType)object;
                return target.getSrc();
            }

            public void setValue(Object object, Object value)
                throws IllegalStateException, IllegalArgumentException
            {
                try
                {
                    AudioType target = (AudioType)object;
                    target.setSrc((String)value);
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
        desc = new XMLFieldDescriptorImpl(java.lang.String.class, "_alt", "alt", NodeType.Attribute);
        desc.setImmutable(true);
        handler = new XMLFieldHandler() {

            public Object getValue(Object object)
                throws IllegalStateException
            {
                AudioType target = (AudioType)object;
                return target.getAlt();
            }

            public void setValue(Object object, Object value)
                throws IllegalStateException, IllegalArgumentException
            {
                try
                {
                    AudioType target = (AudioType)object;
                    target.setAlt((String)value);
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
        desc = new XMLFieldDescriptorImpl(java.lang.String.class, "_begin", "begin", NodeType.Attribute);
        desc.setImmutable(true);
        handler = new XMLFieldHandler() {

            public Object getValue(Object object)
                throws IllegalStateException
            {
                AudioType target = (AudioType)object;
                return target.getBegin();
            }

            public void setValue(Object object, Object value)
                throws IllegalStateException, IllegalArgumentException
            {
                try
                {
                    AudioType target = (AudioType)object;
                    target.setBegin((String)value);
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
        desc = new XMLFieldDescriptorImpl(java.lang.String.class, "_end", "end", NodeType.Attribute);
        desc.setImmutable(true);
        handler = new XMLFieldHandler() {

            public Object getValue(Object object)
                throws IllegalStateException
            {
                AudioType target = (AudioType)object;
                return target.getEnd();
            }

            public void setValue(Object object, Object value)
                throws IllegalStateException, IllegalArgumentException
            {
                try
                {
                    AudioType target = (AudioType)object;
                    target.setEnd((String)value);
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
        return com.openwave.mms.content.smil.AudioType.class;
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
