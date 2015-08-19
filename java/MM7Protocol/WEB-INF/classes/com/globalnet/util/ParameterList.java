package com.globalnet.util;

import java.io.UnsupportedEncodingException;
import java.util.*;

import javax.mail.internet.HeaderTokenizer;
import javax.mail.internet.ParseException;

// Referenced classes of package javax.mail.internet:
//            HeaderTokenizer, ParseException, MimeUtility

public class ParameterList
{
    private static class ParamEnum
        implements Enumeration
    {

        public boolean hasMoreElements()
        {
            return it.hasNext();
        }

        public Object nextElement()
        {
            return it.next();
        }

        private Iterator it;

        ParamEnum(Iterator iterator)
        {
            it = iterator;
        }
    }

    private static class Value
    {

        String value;
        String encodedValue;

        private Value()
        {
        }

    }


    public ParameterList()
    {
        list = new LinkedHashMap();
    }

    public ParameterList(String s)
        throws ParseException
    {
        list = new LinkedHashMap();
        HeaderTokenizer headertokenizer = new HeaderTokenizer(s, "()<>@,;:\\\"\t []/?=");
        do
        {
            HeaderTokenizer.Token token = headertokenizer.next();
            int i = token.getType();
            if(i == -4)
                return;
            if((char)i == ';')
            {
                token = headertokenizer.next();
                if(token.getType() == -4)
                    return;
                if(token.getType() != -1)
                    throw new ParseException("Expected parameter name, got \"" + token.getValue() + "\"");
                String s1 = token.getValue().toLowerCase();
                token = headertokenizer.next();
                if((char)token.getType() != '=')
                    throw new ParseException("Expected '=', got \"" + token.getValue() + "\"");
                token = headertokenizer.next();
                int j = token.getType();
                if(j != -1 && j != -2)
                    throw new ParseException("Expected parameter value, got \"" + token.getValue() + "\"");
                String s2 = token.getValue();
                if(decodeParameters && s1.endsWith("*"))
                {
                    s1 = s1.substring(0, s1.length() - 1);
                    list.put(s1, decodeValue(s2));
                } else
                {
                    list.put(s1, s2);
                }
            } else
            {
                throw new ParseException("Expected ';', got \"" + token.getValue() + "\"");
            }
        } while(true);
    }

    public int size()
    {
        return list.size();
    }

    public String get(String s)
    {
        Object obj = list.get(s.trim().toLowerCase());
        String s1;
        if(obj instanceof Value)
            s1 = ((Value)obj).value;
        else
            s1 = (String)obj;
        return s1;
    }

    public void set(String s, String s1)
    {
        list.put(s.trim().toLowerCase(), s1);
    }

    public void set(String s, String s1, String s2)
    {
        if(encodeParameters)
        {
            Value value = encodeValue(s1, s2);
            if(value != null)
                list.put(s.trim().toLowerCase(), value);
            else
                set(s, s1);
        } else
        {
            set(s, s1);
        }
    }

    public void remove(String s)
    {
        list.remove(s.trim().toLowerCase());
    }

    public Enumeration getNames()
    {
        return new ParamEnum(list.keySet().iterator());
    }

    public String toString()
    {
        return toString(0);
    }

    public String toString(int i)
    {
        StringBuffer stringbuffer = new StringBuffer();
        for(Iterator iterator = list.keySet().iterator(); iterator.hasNext();)
        {
            String s = (String)iterator.next();
            Object obj = list.get(s);
            String s1;
            if(obj instanceof Value)
            {
                s1 = ((Value)obj).encodedValue;
                s = s + '*';
            } else
            {
                s1 = (String)obj;
            }
            s1 = quote(s1);
            stringbuffer.append("; ");
            i += 2;
            int j = s.length() + s1.length() + 1;
            if(i + j > 76)
            {
                stringbuffer.append("\r\n\t");
                i = 8;
            }
            stringbuffer.append(s).append('=');
            i += s.length() + 1;
            if(i + s1.length() > 76)
            {
                String s2 = MimeUtility.fold(i, s1);
                stringbuffer.append(s2);
                int k = s2.lastIndexOf('\n');
                if(k >= 0)
                    i += s2.length() - k - 1;
                else
                    i += s2.length();
            } else
            {
                stringbuffer.append(s1);
                i += s1.length();
            }
        }

        return stringbuffer.toString();
    }

    private String quote(String s)
    {
        return MimeUtility.quote(s, "()<>@,;:\\\"\t []/?=");
    }

    private Value encodeValue(String s, String s1)
    {
        if(MimeUtility.checkAscii(s) == 1)
            return null;
        byte abyte0[];
        try
        {
            abyte0 = s.getBytes(MimeUtility.javaCharset(s1));
        }
        catch(UnsupportedEncodingException unsupportedencodingexception)
        {
            return null;
        }
        StringBuffer stringbuffer = new StringBuffer(abyte0.length + s1.length() + 2);
        stringbuffer.append(s1).append("''");
        for(int i = 0; i < abyte0.length; i++)
        {
            char c = (char)(abyte0[i] & 0xff);
            if(c <= ' ' || c >= '\177' || c == '*' || c == '\'' || c == '%' || "()<>@,;:\\\"\t []/?=".indexOf(c) >= 0)
                stringbuffer.append('%').append(hex[c >> 4]).append(hex[c & 0xf]);
            else
                stringbuffer.append(c);
        }

        Value value = new Value();
        value.value = s;
        value.encodedValue = stringbuffer.toString();
        return value;
    }

    private Value decodeValue(String s)
        throws ParseException
    {
        Value value = new Value();
        value.encodedValue = s;
        value.value = s;
        try
        {
            int i = s.indexOf('\'');
            if(i <= 0)
                if(decodeParametersStrict)
                    throw new ParseException("Missing charset in encoded value: " + s);
                else
                    return value;
            String s1 = s.substring(0, i);
            int j = s.indexOf('\'', i + 1);
            if(j < 0)
                if(decodeParametersStrict)
                    throw new ParseException("Missing language in encoded value: " + s);
                else
                    return value;
            String s2 = s.substring(i + 1, j);
            s = s.substring(j + 1);
            byte abyte0[] = new byte[s.length()];
            i = 0;
            int k = 0;
            for(; i < s.length(); i++)
            {
                char c = s.charAt(i);
                if(c == '%')
                {
                    String s3 = s.substring(i + 1, i + 3);
                    c = (char)Integer.parseInt(s3, 16);
                    i += 2;
                }
                abyte0[k++] = (byte)c;
            }

            value.value = new String(abyte0, 0, k, MimeUtility.javaCharset(s1));
        }
        catch(NumberFormatException numberformatexception)
        {
            if(decodeParametersStrict)
                throw new ParseException(numberformatexception.toString());
        }
        catch(UnsupportedEncodingException unsupportedencodingexception)
        {
            if(decodeParametersStrict)
                throw new ParseException(unsupportedencodingexception.toString());
        }
        catch(StringIndexOutOfBoundsException stringindexoutofboundsexception)
        {
            if(decodeParametersStrict)
                throw new ParseException(stringindexoutofboundsexception.toString());
        }
        return value;
    }

    private Map list;
    private static boolean encodeParameters = false;
    private static boolean decodeParameters = false;
    private static boolean decodeParametersStrict = false;
    private static final char hex[] = {
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 
        'A', 'B', 'C', 'D', 'E', 'F'
    };

    static 
    {
        try
        {
            String s = System.getProperty("mail.mime.encodeparameters");
            encodeParameters = s != null && s.equalsIgnoreCase("true");
            s = System.getProperty("mail.mime.decodeparameters");
            decodeParameters = s != null && s.equalsIgnoreCase("true");
            s = System.getProperty("mail.mime.decodeparameters.strict");
            decodeParametersStrict = s != null && s.equalsIgnoreCase("true");
        }
        catch(SecurityException securityexception) { }
    }
}
