// Decompiled by Jad v1.5.8g. Copyright 2001 Pavel Kouznetsov.
// Jad home page: http://www.kpdus.com/jad.html
// Decompiler options: packimports(3) 
// Source File Name:   ParTypeDescriptor.java

package com.openwave.mms.content.smil;

import org.exolab.castor.mapping.*;
import org.exolab.castor.xml.*;
import org.exolab.castor.xml.util.XMLClassDescriptorImpl;
import org.exolab.castor.xml.util.XMLFieldDescriptorImpl;
import org.exolab.castor.xml.validators.StringValidator;

// Referenced classes of package com.openwave.mms.content.smil:
//            ParType, Img, Video, Text, 
//            Audio, Ref

public class ParTypeDescriptor extends XMLClassDescriptorImpl
{

    public ParTypeDescriptor()
    {
        xmlName = "parType";
        XMLFieldDescriptorImpl desc = null;
        XMLFieldHandler handler = null;
        FieldValidator fieldValidator = null;
        desc = new XMLFieldDescriptorImpl(java.lang.String.class, "_dur", "dur", NodeType.Attribute);
        desc.setImmutable(true);
        handler = new XMLFieldHandler() {

            public Object getValue(Object object)
                throws IllegalStateException
            {
                ParType target = (ParType)object;
                return target.getDur();
            }

            public void setValue(Object object, Object value)
                throws IllegalStateException, IllegalArgumentException
            {
                try
                {
                    ParType target = (ParType)object;
                    target.setDur((String)value);
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
        desc = new XMLFieldDescriptorImpl(com.openwave.mms.content.smil.Img.class, "_img", "img", NodeType.Element);
        handler = new XMLFieldHandler() {

            public Object getValue(Object object)
                throws IllegalStateException
            {
                ParType target = (ParType)object;
                return target.getImg();
            }

            public void setValue(Object object, Object value)
                throws IllegalStateException, IllegalArgumentException
            {
                try
                {
                    ParType target = (ParType)object;
                    target.setImg((Img)value);
                }
                catch(Exception ex)
                {
                    throw new IllegalStateException(ex.toString());
                }
            }

            public Object newInstance(Object parent)
            {
                return new Img();
            }

        }
;
        desc.setHandler(handler);
        desc.setMultivalued(false);
        addFieldDescriptor(desc);
        fieldValidator = new FieldValidator();
        desc.setValidator(fieldValidator);
        desc = new XMLFieldDescriptorImpl(com.openwave.mms.content.smil.Video.class, "_video", "video", NodeType.Element);
        handler = new XMLFieldHandler() {

            public Object getValue(Object object)
                throws IllegalStateException
            {
                ParType target = (ParType)object;
                return target.getVideo();
            }

            public void setValue(Object object, Object value)
                throws IllegalStateException, IllegalArgumentException
            {
                try
                {
                    ParType target = (ParType)object;
                    target.setVideo((Video)value);
                }
                catch(Exception ex)
                {
                    throw new IllegalStateException(ex.toString());
                }
            }

            public Object newInstance(Object parent)
            {
                return new Video();
            }

        }
;
        desc.setHandler(handler);
        desc.setMultivalued(false);
        addFieldDescriptor(desc);
        fieldValidator = new FieldValidator();
        desc.setValidator(fieldValidator);
        desc = new XMLFieldDescriptorImpl(com.openwave.mms.content.smil.Text.class, "_text", "text", NodeType.Element);
        handler = new XMLFieldHandler() {

            public Object getValue(Object object)
                throws IllegalStateException
            {
                ParType target = (ParType)object;
                return target.getText();
            }

            public void setValue(Object object, Object value)
                throws IllegalStateException, IllegalArgumentException
            {
                try
                {
                    ParType target = (ParType)object;
                    target.setText((Text)value);
                }
                catch(Exception ex)
                {
                    throw new IllegalStateException(ex.toString());
                }
            }

            public Object newInstance(Object parent)
            {
                return new Text();
            }

        }
;
        desc.setHandler(handler);
        desc.setMultivalued(false);
        addFieldDescriptor(desc);
        fieldValidator = new FieldValidator();
        desc.setValidator(fieldValidator);
        desc = new XMLFieldDescriptorImpl(com.openwave.mms.content.smil.Audio.class, "_audio", "audio", NodeType.Element);
        handler = new XMLFieldHandler() {

            public Object getValue(Object object)
                throws IllegalStateException
            {
                ParType target = (ParType)object;
                return target.getAudio();
            }

            public void setValue(Object object, Object value)
                throws IllegalStateException, IllegalArgumentException
            {
                try
                {
                    ParType target = (ParType)object;
                    target.setAudio((Audio)value);
                }
                catch(Exception ex)
                {
                    throw new IllegalStateException(ex.toString());
                }
            }

            public Object newInstance(Object parent)
            {
                return new Audio();
            }

        }
;
        desc.setHandler(handler);
        desc.setMultivalued(false);
        addFieldDescriptor(desc);
        fieldValidator = new FieldValidator();
        desc.setValidator(fieldValidator);
        desc = new XMLFieldDescriptorImpl(com.openwave.mms.content.smil.Ref.class, "_ref", "ref", NodeType.Element);
        handler = new XMLFieldHandler() {

            public Object getValue(Object object)
                throws IllegalStateException
            {
                ParType target = (ParType)object;
                return target.getRef();
            }

            public void setValue(Object object, Object value)
                throws IllegalStateException, IllegalArgumentException
            {
                try
                {
                    ParType target = (ParType)object;
                    target.setRef((Ref)value);
                }
                catch(Exception ex)
                {
                    throw new IllegalStateException(ex.toString());
                }
            }

            public Object newInstance(Object parent)
            {
                return new Ref();
            }

        }
;
        desc.setHandler(handler);
        desc.setMultivalued(false);
        addFieldDescriptor(desc);
        fieldValidator = new FieldValidator();
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
        return com.openwave.mms.content.smil.ParType.class;
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
