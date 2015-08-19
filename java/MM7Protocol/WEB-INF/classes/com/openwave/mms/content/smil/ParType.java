// Decompiled by Jad v1.5.8g. Copyright 2001 Pavel Kouznetsov.
// Jad home page: http://www.kpdus.com/jad.html
// Decompiler options: packimports(3) 
// Source File Name:   ParType.java

package com.openwave.mms.content.smil;

import java.io.*;
import org.exolab.castor.xml.*;
import org.xml.sax.ContentHandler;

// Referenced classes of package com.openwave.mms.content.smil:
//            Img, Video, Text, Audio, 
//            Ref

public abstract class ParType
    implements Serializable
{

    public ParType()
    {
    }

    public Audio getAudio()
    {
        return _audio;
    }

    public String getDur()
    {
        return _dur;
    }

    public Img getImg()
    {
        return _img;
    }

    public Ref getRef()
    {
        return _ref;
    }

    public Text getText()
    {
        return _text;
    }

    public Video getVideo()
    {
        return _video;
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

    public void setAudio(Audio audio)
    {
        _audio = audio;
    }

    public void setDur(String dur)
    {
        _dur = dur;
    }

    public void setImg(Img img)
    {
        _img = img;
    }

    public void setRef(Ref ref)
    {
        _ref = ref;
    }

    public void setText(Text text)
    {
        _text = text;
    }

    public void setVideo(Video video)
    {
        _video = video;
    }

    public void validate()
        throws ValidationException
    {
        Validator validator = new Validator();
        validator.validate(this);
    }

    private String _dur;
    private Img _img;
    private Video _video;
    private Text _text;
    private Audio _audio;
    private Ref _ref;
}
