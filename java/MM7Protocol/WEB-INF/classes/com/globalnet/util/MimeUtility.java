// Decompiled by Jad v1.5.8g. Copyright 2001 Pavel Kouznetsov.
// Jad home page: http://www.kpdus.com/jad.html
// Decompiler options: packimports(3) 
// Source File Name:   MimeUtility.java
package com.globalnet.util;

import com.sun.mail.util.*;
import java.io.*;
import java.util.*;
import javax.activation.DataHandler;
import javax.activation.DataSource;
import javax.mail.MessagingException;
import javax.mail.internet.ParseException;

// Referenced classes of package javax.mail.internet:
//            ContentType, AsciiOutputStream, ParseException

public class MimeUtility
{

    private MimeUtility()
    {
    }

    public static String getEncoding(DataSource datasource)
    {
        ContentType contenttype = null;
        InputStream inputstream = null;
        String s = null;
        try
        {
            contenttype = new ContentType(datasource.getContentType());
            inputstream = datasource.getInputStream();
        }
        catch(Exception exception)
        {
            return "base64";
        }
        boolean flag = contenttype.match("text/*");
        int i = checkAscii(inputstream, -1, !flag);
        switch(i)
        {
        case 1: // '\001'
            s = "7bit";
            break;

        case 2: // '\002'
            s = "quoted-printable";
            break;

        default:
            s = "base64";
            break;
        }
        try
        {
            inputstream.close();
        }
        catch(IOException ioexception) { }
        return s;
    }

    public static InputStream decode(InputStream inputstream, String s)
        throws MessagingException
    {
        if(s.equalsIgnoreCase("base64"))
            return new BASE64DecoderStream(inputstream);
        if(s.equalsIgnoreCase("quoted-printable"))
            return new QPDecoderStream(inputstream);
        if(s.equalsIgnoreCase("uuencode") || s.equalsIgnoreCase("x-uuencode") || s.equalsIgnoreCase("x-uue"))
            return new UUDecoderStream(inputstream);
        if(s.equalsIgnoreCase("binary") || s.equalsIgnoreCase("7bit") || s.equalsIgnoreCase("8bit"))
            return inputstream;
        else
            throw new MessagingException("Unknown encoding: " + s);
    }

    public static OutputStream encode(OutputStream outputstream, String s)
        throws MessagingException
    {
        if(s == null)
            return outputstream;
        if(s.equalsIgnoreCase("base64"))
            return new BASE64EncoderStream(outputstream);
        if(s.equalsIgnoreCase("quoted-printable"))
            return new QPEncoderStream(outputstream);
        if(s.equalsIgnoreCase("uuencode") || s.equalsIgnoreCase("x-uuencode") || s.equalsIgnoreCase("x-uue"))
            return new UUEncoderStream(outputstream);
        if(s.equalsIgnoreCase("binary") || s.equalsIgnoreCase("7bit") || s.equalsIgnoreCase("8bit"))
            return outputstream;
        else
            throw new MessagingException("Unknown encoding: " + s);
    }

    public static OutputStream encode(OutputStream outputstream, String s, String s1)
        throws MessagingException
    {
        if(s == null)
            return outputstream;
        if(s.equalsIgnoreCase("base64"))
            return new BASE64EncoderStream(outputstream);
        if(s.equalsIgnoreCase("quoted-printable"))
            return new QPEncoderStream(outputstream);
        if(s.equalsIgnoreCase("uuencode") || s.equalsIgnoreCase("x-uuencode") || s.equalsIgnoreCase("x-uue"))
            return new UUEncoderStream(outputstream, s1);
        if(s.equalsIgnoreCase("binary") || s.equalsIgnoreCase("7bit") || s.equalsIgnoreCase("8bit"))
            return outputstream;
        else
            throw new MessagingException("Unknown encoding: " + s);
    }

    public static String encodeText(String s)
        throws UnsupportedEncodingException
    {
        return encodeText(s, null, null);
    }

    public static String encodeText(String s, String s1, String s2)
        throws UnsupportedEncodingException
    {
        return encodeWord(s, s1, s2, false);
    }

    public static String decodeText(String s)
        throws UnsupportedEncodingException
    {
        String s1 = " \t\n\r";
        if(s.indexOf("=?") == -1)
            return s;
        StringTokenizer stringtokenizer = new StringTokenizer(s, s1, true);
        StringBuffer stringbuffer = new StringBuffer();
        StringBuffer stringbuffer1 = new StringBuffer();
        boolean flag = false;
        while(stringtokenizer.hasMoreTokens()) 
        {
            String s2 = stringtokenizer.nextToken();
            char c;
            if((c = s2.charAt(0)) == ' ' || c == '\t' || c == '\r' || c == '\n')
            {
                stringbuffer1.append(c);
            } else
            {
                String s3;
                try
                {
                    s3 = decodeWord(s2);
                    if(!flag && stringbuffer1.length() > 0)
                        stringbuffer.append(stringbuffer1);
                    flag = true;
                }
                catch(ParseException parseexception)
                {
                    s3 = s2;
                    if(!decodeStrict)
                        s3 = decodeInnerWords(s3);
                    if(stringbuffer1.length() > 0)
                        stringbuffer.append(stringbuffer1);
                    flag = false;
                }
                stringbuffer.append(s3);
                stringbuffer1.setLength(0);
            }
        }
        return stringbuffer.toString();
    }

    public static String encodeWord(String s)
        throws UnsupportedEncodingException
    {
        return encodeWord(s, null, null);
    }

    public static String encodeWord(String s, String s1, String s2)
        throws UnsupportedEncodingException
    {
        return encodeWord(s, s1, s2, true);
    }

    private static String encodeWord(String s, String s1, String s2, boolean flag)
        throws UnsupportedEncodingException
    {
        int i = checkAscii(s);
        if(i == 1)
            return s;
        String s3;
        if(s1 == null)
        {
            s3 = getDefaultJavaCharset();
            s1 = getDefaultMIMECharset();
        } else
        {
            s3 = javaCharset(s1);
        }
        if(s2 == null)
            if(i != 3)
                s2 = "Q";
            else
                s2 = "B";
        boolean flag1;
        if(s2.equalsIgnoreCase("B"))
            flag1 = true;
        else
        if(s2.equalsIgnoreCase("Q"))
            flag1 = false;
        else
            throw new UnsupportedEncodingException("Unknown transfer encoding: " + s2);
        StringBuffer stringbuffer = new StringBuffer();
        doEncode(s, flag1, s3, 68 - s1.length(), "=?" + s1 + "?" + s2 + "?", true, flag, stringbuffer);
        return stringbuffer.toString();
    }

    private static void doEncode(String s, boolean flag, String s1, int i, String s2, boolean flag1, boolean flag2, StringBuffer stringbuffer)
        throws UnsupportedEncodingException
    {
        byte abyte0[] = s.getBytes(s1);
        int j;
        if(flag)
            j = BEncoderStream.encodedLength(abyte0);
        else
            j = QEncoderStream.encodedLength(abyte0, flag2);
        int k;
        if(j > i && (k = s.length()) > 1)
        {
            doEncode(s.substring(0, k / 2), flag, s1, i, s2, flag1, flag2, stringbuffer);
            doEncode(s.substring(k / 2, k), flag, s1, i, s2, false, flag2, stringbuffer);
        } else
        {
            ByteArrayOutputStream bytearrayoutputstream = new ByteArrayOutputStream();
            Object obj;
            if(flag)
                obj = new BEncoderStream(bytearrayoutputstream);
            else
                obj = new QEncoderStream(bytearrayoutputstream, flag2);
            try
            {
                ((OutputStream) (obj)).write(abyte0);
                ((OutputStream) (obj)).close();
            }
            catch(IOException ioexception) { }
            byte abyte1[] = bytearrayoutputstream.toByteArray();
            if(!flag1)
                if(foldEncodedWords)
                    stringbuffer.append("\r\n ");
                else
                    stringbuffer.append(" ");
            stringbuffer.append(s2);
            for(int l = 0; l < abyte1.length; l++)
                stringbuffer.append((char)abyte1[l]);

            stringbuffer.append("?=");
        }
    }

    public static String decodeWord(String s)
        throws ParseException, UnsupportedEncodingException
    {
        if(!s.startsWith("=?"))
            throw new ParseException();
        int i = 2;
        int j;
        if((j = s.indexOf('?', i)) == -1)
            throw new ParseException();
        String s1 = javaCharset(s.substring(i, j));
        i = j + 1;
        if((j = s.indexOf('?', i)) == -1)
            throw new ParseException();
        String s2 = s.substring(i, j);
        i = j + 1;
        if((j = s.indexOf("?=", i)) == -1)
            throw new ParseException();
        String s3 = s.substring(i, j);
        try
        {
            String s4;
            if(s3.length() > 0)
            {
                ByteArrayInputStream bytearrayinputstream = new ByteArrayInputStream(ASCIIUtility.getBytes(s3));
                Object obj;
                if(s2.equalsIgnoreCase("B"))
                    obj = new BASE64DecoderStream(bytearrayinputstream);
                else
                if(s2.equalsIgnoreCase("Q"))
                    obj = new QDecoderStream(bytearrayinputstream);
                else
                    throw new UnsupportedEncodingException("unknown encoding: " + s2);
                int k = bytearrayinputstream.available();
                byte abyte0[] = new byte[k];
                k = ((InputStream) (obj)).read(abyte0, 0, k);
                s4 = k > 0 ? new String(abyte0, 0, k, s1) : "";
            } else
            {
                s4 = "";
            }
            if(j + 2 < s.length())
            {
                String s5 = s.substring(j + 2);
                if(!decodeStrict)
                    s5 = decodeInnerWords(s5);
                s4 = s4 + s5;
            }
            return s4;
        }
        catch(UnsupportedEncodingException unsupportedencodingexception)
        {
            throw unsupportedencodingexception;
        }
        catch(IOException ioexception)
        {
            throw new ParseException();
        }
        catch(IllegalArgumentException illegalargumentexception)
        {
            throw new UnsupportedEncodingException();
        }
    }

    private static String decodeInnerWords(String s)
        throws UnsupportedEncodingException
    {
        int i = 0;
        StringBuffer stringbuffer = new StringBuffer();
        int j;
        while((j = s.indexOf("=?", i)) >= 0) 
        {
            stringbuffer.append(s.substring(i, j));
            int k = s.indexOf("?=", j);
            if(k < 0)
                break;
            String s1 = s.substring(j, k + 2);
            try
            {
                s1 = decodeWord(s1);
            }
            catch(ParseException parseexception) { }
            stringbuffer.append(s1);
            i = k + 2;
        }
        if(i == 0)
            return s;
        if(i < s.length())
            stringbuffer.append(s.substring(i));
        return stringbuffer.toString();
    }

    public static String quote(String s, String s1)
    {
        int i = s.length();
        boolean flag = false;
        for(int j = 0; j < i; j++)
        {
            char c = s.charAt(j);
            if(c == '"' || c == '\\' || c == '\r' || c == '\n')
            {
                StringBuffer stringbuffer1 = new StringBuffer(i + 3);
                stringbuffer1.append('"');
                stringbuffer1.append(s.substring(0, j));
                int k = 0;
                for(int l = j; l < i; l++)
                {
                    char c1 = s.charAt(l);
                    if((c1 == '"' || c1 == '\\' || c1 == '\r' || c1 == '\n') && (c1 != '\n' || k != 13))
                        stringbuffer1.append('\\');
                    stringbuffer1.append(c1);
                    k = c1;
                }

                stringbuffer1.append('"');
                return stringbuffer1.toString();
            }
            if(c < ' ' || c >= '\177' || s1.indexOf(c) >= 0)
                flag = true;
        }

        if(flag)
        {
            StringBuffer stringbuffer = new StringBuffer(i + 2);
            stringbuffer.append('"').append(s).append('"');
            return stringbuffer.toString();
        } else
        {
            return s;
        }
    }

    public static String fold(int i, String s)
    {
        if(!foldText)
            return s;
        int j;
        for(j = s.length() - 1; j >= 0; j--)
        {
            char c = s.charAt(j);
            if(c != ' ' && c != '\t' && c != '\r' && c != '\n')
                break;
        }

        if(j != s.length() - 1)
            s = s.substring(0, j + 1);
        if(i + s.length() <= 76)
            return s;
        StringBuffer stringbuffer = new StringBuffer(s.length() + 4);
        char c2 = '\0';
        for(; i + s.length() > 76; i = 1)
        {
            int k = -1;
            for(int l = 0; l < s.length(); l++)
            {
                if(k != -1 && i + l > 76)
                    break;
                char c1 = s.charAt(l);
                if((c1 == ' ' || c1 == '\t') && c2 != ' ' && c2 != '\t')
                    k = l;
                c2 = c1;
            }

            if(k == -1)
            {
                stringbuffer.append(s);
                s = "";
                i = 0;
                break;
            }
            stringbuffer.append(s.substring(0, k));
            stringbuffer.append("\r\n");
            c2 = s.charAt(k);
            stringbuffer.append(c2);
            s = s.substring(k + 1);
        }

        stringbuffer.append(s);
        return stringbuffer.toString();
    }

    public static String unfold(String s)
    {
        if(!foldText)
            return s;
        StringBuffer stringbuffer = null;
        int i;
        while((i = indexOfAny(s, "\r\n")) >= 0) 
        {
            int j = i;
            int k = s.length();
            if(++i < k && s.charAt(i - 1) == '\r' && s.charAt(i) == '\n')
                i++;
            char c;
            if(j == 0 || s.charAt(j - 1) != '\\')
            {
                if(i < k && ((c = s.charAt(i)) == ' ' || c == '\t'))
                {
                    char c1;
                    for(i++; i < k && ((c1 = s.charAt(i)) == ' ' || c1 == '\t'); i++);
                    if(stringbuffer == null)
                        stringbuffer = new StringBuffer(s.length());
                    if(j != 0)
                    {
                        stringbuffer.append(s.substring(0, j));
                        stringbuffer.append(' ');
                    }
                    s = s.substring(i);
                } else
                {
                    if(stringbuffer == null)
                        stringbuffer = new StringBuffer(s.length());
                    stringbuffer.append(s.substring(0, i));
                    s = s.substring(i);
                }
            } else
            {
                if(stringbuffer == null)
                    stringbuffer = new StringBuffer(s.length());
                stringbuffer.append(s.substring(0, j - 1));
                stringbuffer.append(s.substring(j, i));
                s = s.substring(i);
            }
        }
        if(stringbuffer != null)
        {
            stringbuffer.append(s);
            return stringbuffer.toString();
        } else
        {
            return s;
        }
    }

    private static int indexOfAny(String s, String s1)
    {
        return indexOfAny(s, s1, 0);
    }

    private static int indexOfAny(String s, String s1, int i)
    {
        try
        {
            int j = s.length();
            for(int k = i; k < j; k++)
                if(s1.indexOf(s.charAt(k)) >= 0)
                    return k;

            return -1;
        }
        catch(StringIndexOutOfBoundsException stringindexoutofboundsexception)
        {
            return -1;
        }
    }

    public static String javaCharset(String s)
    {
        if(mime2java == null || s == null)
        {
            return s;
        } else
        {
            String s1 = (String)mime2java.get(s.toLowerCase());
            return s1 != null ? s1 : s;
        }
    }

    public static String mimeCharset(String s)
    {
        if(java2mime == null || s == null)
        {
            return s;
        } else
        {
            String s1 = (String)java2mime.get(s.toLowerCase());
            return s1 != null ? s1 : s;
        }
    }

    public static String getDefaultJavaCharset()
    {
        if(defaultJavaCharset == null)
        {
            String s = null;
            try
            {
                s = System.getProperty("mail.mime.charset");
            }
            catch(SecurityException securityexception) { }
            if(s != null && s.length() > 0)
            {
                defaultJavaCharset = javaCharset(s);
                return defaultJavaCharset;
            }
            try
            {
                defaultJavaCharset = System.getProperty("file.encoding", "8859_1");
            }
            catch(SecurityException securityexception1)
            {
                class NullInputStream extends InputStream
                {

                    public int read()
                    {
                        return 0;
                    }

            NullInputStream()
            {
            }
                }

                InputStreamReader inputstreamreader = new InputStreamReader(new NullInputStream());
                defaultJavaCharset = inputstreamreader.getEncoding();
                if(defaultJavaCharset == null)
                    defaultJavaCharset = "8859_1";
            }
        }
        return defaultJavaCharset;
    }

    static String getDefaultMIMECharset()
    {
        if(defaultMIMECharset == null)
            try
            {
                defaultMIMECharset = System.getProperty("mail.mime.charset");
            }
            catch(SecurityException securityexception) { }
        if(defaultMIMECharset == null)
            defaultMIMECharset = mimeCharset(getDefaultJavaCharset());
        return defaultMIMECharset;
    }

    private static void loadMappings(LineInputStream lineinputstream, Hashtable hashtable)
    {
        while(true) 
        {
            String s;
            try
            {
                s = lineinputstream.readLine();
            }
            catch(IOException ioexception)
            {
                break;
            }
            if(s == null || s.startsWith("--") && s.endsWith("--"))
                break;
            if(s.trim().length() != 0 && !s.startsWith("#"))
            {
                StringTokenizer stringtokenizer = new StringTokenizer(s, " \t");
                try
                {
                    String s1 = stringtokenizer.nextToken();
                    String s2 = stringtokenizer.nextToken();
                    hashtable.put(s1.toLowerCase(), s2);
                }
                catch(NoSuchElementException nosuchelementexception) { }
            }
        }
    }

    static int checkAscii(String s)
    {
        int i = 0;
        int j = 0;
        int k = s.length();
        for(int l = 0; l < k; l++)
            if(nonascii(s.charAt(l)))
                j++;
            else
                i++;

        if(j == 0)
            return 1;
        return i <= j ? 3 : 2;
    }

    static int checkAscii(byte abyte0[])
    {
        int i = 0;
        int j = 0;
        for(int k = 0; k < abyte0.length; k++)
            if(nonascii(abyte0[k] & 0xff))
                j++;
            else
                i++;

        if(j == 0)
            return 1;
        return i <= j ? 3 : 2;
    }

    static int checkAscii(InputStream inputstream, int i, boolean flag)
    {
        int j = 0;
        int k = 0;
        int i1 = 4096;
        int j1 = 0;
        boolean flag1 = false;
        boolean flag2 = false;
        boolean flag3 = encodeEolStrict && flag;
        byte abyte0[] = null;
        if(i != 0)
        {
            i1 = i != -1 ? Math.min(i, 4096) : 4096;
            abyte0 = new byte[i1];
        }
        while(i != 0) 
        {
            int l;
            try
            {
                if((l = inputstream.read(abyte0, 0, i1)) == -1)
                    break;
                int k1 = 0;
                for(int l1 = 0; l1 < l; l1++)
                {
                    int i2 = abyte0[l1] & 0xff;
                    if(flag3 && (k1 == 13 && i2 != 10 || k1 != 13 && i2 == 10))
                        flag2 = true;
                    if(i2 == 13 || i2 == 10)
                        j1 = 0;
                    else
                    if(++j1 > 998)
                        flag1 = true;
                    if(nonascii(i2))
                    {
                        if(flag)
                            return 3;
                        k++;
                    } else
                    {
                        j++;
                    }
                    k1 = i2;
                }

            }
            catch(IOException ioexception)
            {
                break;
            }
            if(i != -1)
                i -= l;
        }
        if(i == 0 && flag)
            return 3;
        if(k == 0)
        {
            if(flag2)
                return 3;
            return !flag1 ? 1 : 2;
        }
        return j <= k ? 3 : 2;
    }

    static final boolean nonascii(int i)
    {
        return i >= 127 || i < 32 && i != 13 && i != 10 && i != 9;
    }

    static Class _mthclass$(String s)
    {
        try
        {
            return Class.forName(s);
        }
        catch(ClassNotFoundException classnotfoundexception)
        {
            throw new NoClassDefFoundError(classnotfoundexception.getMessage());
        }
    }

    public static final int ALL = -1;
    private static boolean decodeStrict = true;
    private static boolean encodeEolStrict = false;
    private static boolean foldEncodedWords = false;
    private static boolean foldText = true;
    private static String defaultJavaCharset;
    private static String defaultMIMECharset;
    private static Hashtable mime2java;
    private static Hashtable java2mime;
    static final int ALL_ASCII = 1;
    static final int MOSTLY_ASCII = 2;
    static final int MOSTLY_NONASCII = 3;

    static 
    {
        try
        {
            String s = System.getProperty("mail.mime.decodetext.strict");
            decodeStrict = s == null || !s.equalsIgnoreCase("false");
            s = System.getProperty("mail.mime.encodeeol.strict");
            encodeEolStrict = s != null && s.equalsIgnoreCase("true");
            s = System.getProperty("mail.mime.foldencodedwords");
            foldEncodedWords = s != null && s.equalsIgnoreCase("true");
            s = System.getProperty("mail.mime.foldtext");
            foldText = s == null || !s.equalsIgnoreCase("false");
        }
        catch(SecurityException securityexception) { }
        java2mime = new Hashtable(40);
        mime2java = new Hashtable(10);
        try
        {
            Object obj = (javax.mail.internet.MimeUtility.class).getResourceAsStream("/META-INF/javamail.charset.map");
            if(obj != null)
                try
                {
                    obj = new LineInputStream(((InputStream) (obj)));
                    loadMappings((LineInputStream)obj, java2mime);
                    loadMappings((LineInputStream)obj, mime2java);
                }
                finally
                {
                    try
                    {
                        ((InputStream) (obj)).close();
                    }
                    catch(Exception exception2) { }
                }
        }
        catch(Exception exception) { }
        if(java2mime.isEmpty())
        {
            java2mime.put("8859_1", "ISO-8859-1");
            java2mime.put("iso8859_1", "ISO-8859-1");
            java2mime.put("iso8859-1", "ISO-8859-1");
            java2mime.put("8859_2", "ISO-8859-2");
            java2mime.put("iso8859_2", "ISO-8859-2");
            java2mime.put("iso8859-2", "ISO-8859-2");
            java2mime.put("8859_3", "ISO-8859-3");
            java2mime.put("iso8859_3", "ISO-8859-3");
            java2mime.put("iso8859-3", "ISO-8859-3");
            java2mime.put("8859_4", "ISO-8859-4");
            java2mime.put("iso8859_4", "ISO-8859-4");
            java2mime.put("iso8859-4", "ISO-8859-4");
            java2mime.put("8859_5", "ISO-8859-5");
            java2mime.put("iso8859_5", "ISO-8859-5");
            java2mime.put("iso8859-5", "ISO-8859-5");
            java2mime.put("8859_6", "ISO-8859-6");
            java2mime.put("iso8859_6", "ISO-8859-6");
            java2mime.put("iso8859-6", "ISO-8859-6");
            java2mime.put("8859_7", "ISO-8859-7");
            java2mime.put("iso8859_7", "ISO-8859-7");
            java2mime.put("iso8859-7", "ISO-8859-7");
            java2mime.put("8859_8", "ISO-8859-8");
            java2mime.put("iso8859_8", "ISO-8859-8");
            java2mime.put("iso8859-8", "ISO-8859-8");
            java2mime.put("8859_9", "ISO-8859-9");
            java2mime.put("iso8859_9", "ISO-8859-9");
            java2mime.put("iso8859-9", "ISO-8859-9");
            java2mime.put("sjis", "Shift_JIS");
            java2mime.put("jis", "ISO-2022-JP");
            java2mime.put("iso2022jp", "ISO-2022-JP");
            java2mime.put("euc_jp", "euc-jp");
            java2mime.put("koi8_r", "koi8-r");
            java2mime.put("euc_cn", "euc-cn");
            java2mime.put("euc_tw", "euc-tw");
            java2mime.put("euc_kr", "euc-kr");
        }
        if(mime2java.isEmpty())
        {
            mime2java.put("iso-2022-cn", "ISO2022CN");
            mime2java.put("iso-2022-kr", "ISO2022KR");
            mime2java.put("utf-8", "UTF8");
            mime2java.put("utf8", "UTF8");
            mime2java.put("ja_jp.iso2022-7", "ISO2022JP");
            mime2java.put("ja_jp.eucjp", "EUCJIS");
            mime2java.put("euc-kr", "KSC5601");
            mime2java.put("euckr", "KSC5601");
            mime2java.put("us-ascii", "ISO-8859-1");
            mime2java.put("x-us-ascii", "ISO-8859-1");
        }
    }
}
