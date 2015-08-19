// Decompiled by Jad v1.5.8g. Copyright 2001 Pavel Kouznetsov.
// Jad home page: http://www.kpdus.com/jad.html
// Decompiler options: packimports(3) 
// Source File Name:   RegionTypeIdType.java

package com.openwave.mms.content.smil.types;

import java.io.Serializable;
import java.util.Enumeration;
import java.util.Hashtable;

public class RegionTypeIdType implements Serializable {

    public static final int IMAGE_TYPE = 0;
    public static final RegionTypeIdType IMAGE = new RegionTypeIdType(0, "Image");
    public static final int TEXT_TYPE = 1;
    public static final RegionTypeIdType TEXT = new RegionTypeIdType(1, "Text");
    private static Hashtable _memberTable = init();

    private int type;
    private String stringValue;

    private RegionTypeIdType(int type, String value) {
        this.type = -1;
        stringValue = null;
        this.type = type;
        stringValue = value;
    }

    public static Enumeration enumerate() {
        return _memberTable.elements();
    }

    public int getType() {
        return type;
    }

    private static Hashtable init() {
        Hashtable members = new Hashtable();
        members.put("Image", IMAGE);
        members.put("Text", TEXT);
        return members;
    }

    public String toString() {
        return stringValue;
    }

    public static RegionTypeIdType valueOf(String string) {
        Object obj = null;
        if (string != null)
            obj = _memberTable.get(string);

        if(obj == null){
        	return TEXT;
//            String err = "'" + string + "' is not a valid RegionTypeIdType";
//            throw new IllegalArgumentException(err);
            
        }else{
            return (RegionTypeIdType)obj;
        }
    }

}
