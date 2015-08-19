package com.globalnet.util;

import javax.mail.internet.HeaderTokenizer;
import javax.mail.internet.ParameterList;
import javax.mail.internet.ParseException;


// Referenced classes of package javax.mail.internet:
//            HeaderTokenizer, ParseException, ParameterList

public class ContentType
{

    public ContentType()
    {
    }

    public ContentType(String s, String s1, ParameterList parameterlist)
    {
        primaryType = s;
        subType = s1;
        list = parameterlist;
    }

    public ContentType(String s)
        throws ParseException
    {
        HeaderTokenizer headertokenizer = new HeaderTokenizer(s, "()<>@,;:\\\"\t []/?=");
        HeaderTokenizer.Token token = headertokenizer.next();
        if(token.getType() != -1)
            throw new ParseException();
        primaryType = token.getValue();
        token = headertokenizer.next();
        if((char)token.getType() != '/')
            throw new ParseException();
        token = headertokenizer.next();
        if(token.getType() != -1)
            throw new ParseException();
        subType = token.getValue();
        String s1 = headertokenizer.getRemainder();
        if(s1 != null)
            list = new ParameterList(s1);
    }

    public String getPrimaryType()
    {
        return primaryType;
    }

    public String getSubType()
    {
        return subType;
    }

    public String getBaseType()
    {
        return primaryType + '/' + subType;
    }

    public String getParameter(String s)
    {
        if(list == null)
            return null;
        else
            return list.get(s);
    }

    public ParameterList getParameterList()
    {
        return list;
    }

    public void setPrimaryType(String s)
    {
        primaryType = s;
    }

    public void setSubType(String s)
    {
        subType = s;
    }

    public void setParameter(String s, String s1)
    {
        if(list == null)
            list = new ParameterList();
        list.set(s, s1);
    }

    public void setParameterList(ParameterList parameterlist)
    {
        list = parameterlist;
    }

    public String toString()
    {
        if(primaryType == null || subType == null)
            return null;
        StringBuffer stringbuffer = new StringBuffer();
        stringbuffer.append(primaryType).append('/').append(subType);
        if(list != null)
            stringbuffer.append(list.toString(stringbuffer.length() + 14));
        return stringbuffer.toString();
    }

    public boolean match(ContentType contenttype)
    {
        if(!primaryType.equalsIgnoreCase(contenttype.getPrimaryType()))
            return false;
        String s = contenttype.getSubType();
        if(subType.charAt(0) == '*' || s.charAt(0) == '*')
            return true;
        return subType.equalsIgnoreCase(s);
    }

    public boolean match(String s)
    {
        try
        {
            return match(new ContentType(s));
        }
        catch(ParseException parseexception)
        {
            return false;
        }
    }

    private String primaryType;
    private String subType;
    private ParameterList list;
}
