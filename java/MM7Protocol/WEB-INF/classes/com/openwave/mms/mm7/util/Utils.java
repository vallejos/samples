package com.openwave.mms.mm7.util;

import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.Calendar;
import java.util.NoSuchElementException;
import java.util.StringTokenizer;

public class Utils {

    public static String generateTransactionID() {
        try {
            // return a globally unique id
            return new java.rmi.server.UID().toString() +
                       java.net.InetAddress.getLocalHost().toString();
        } catch( java.net.UnknownHostException uhe ) {
            // return a unique id on this host
            return new java.rmi.server.UID().toString();
        }
    }

    public static String generateContentID() {
        return "generic_content_id";
    }

    public static String convertDateToISO8601( Calendar date ) {
        StringBuffer dateStr = new StringBuffer();
        dateStr.append( date.get( Calendar.YEAR ) );
        dateStr.append( '-' );
        int fieldValue = date.get( Calendar.MONTH );
        //Month is zero based
        fieldValue++;
        if( fieldValue < 10 ) dateStr.append( '0' );
        dateStr.append( fieldValue );
        dateStr.append( '-' );
        fieldValue = date.get( Calendar.DATE );
        if( fieldValue < 10 ) dateStr.append( '0' );
        dateStr.append( fieldValue );
        dateStr.append( 'T' );
        fieldValue = date.get( Calendar.HOUR_OF_DAY );
        if( fieldValue < 10 ) dateStr.append( '0' );
        dateStr.append( fieldValue );
        dateStr.append( ':' );
        fieldValue = date.get( Calendar.MINUTE );
        if( fieldValue < 10 ) dateStr.append( '0' );
        dateStr.append( fieldValue );
        dateStr.append( ':' );
        fieldValue = date.get( Calendar.SECOND );
        if( fieldValue < 10 ) dateStr.append( '0' );
        dateStr.append( fieldValue );
        fieldValue = date.get( Calendar.ZONE_OFFSET ) + date.get( Calendar.DST_OFFSET );
        if( fieldValue != 0 ) {
            if( fieldValue > 0 )
                dateStr.append( '+' );
            else {
                dateStr.append( '-' );
                fieldValue = -fieldValue;
            }
            fieldValue /= 1000; // offset in secs
            int Minutes = fieldValue / 60;
            int Hours = Minutes / 60;
            Minutes %= 60;
            if( Hours < 10 ) dateStr.append( '0' );
            dateStr.append( Hours + ":" );
            if( Minutes < 10 ) dateStr.append( '0' );
            dateStr.append( Minutes );
        } else dateStr.append( 'Z' );
        return dateStr.toString();
    }

    public static Calendar convertDateFromISO8601( String aDate ) {
        Calendar newDate = Calendar.getInstance();
        StringTokenizer parser = new StringTokenizer( aDate, "-:TZ" );
        try {
            newDate.set( Calendar.YEAR, getNumber( parser ) );
            newDate.set( Calendar.MONTH, getNumber( parser ) - 1 );
            newDate.set( Calendar.DATE, getNumber( parser ) );
            newDate.set( Calendar.HOUR_OF_DAY, getNumber( parser ) );
            newDate.set( Calendar.MINUTE, getNumber( parser ) );
            newDate.set( Calendar.SECOND, getNumber( parser ) );
        } catch( NumberFormatException nfe ) {
            return null;
        } catch( NoSuchElementException nsee ) {
            return null;
        }

        // the incoming time is GMT so adjust it for local time
        int offset = newDate.get( Calendar.ZONE_OFFSET ) + newDate.get( Calendar.DST_OFFSET );
        if( offset != 0 ) {
            offset /= 60*1000; //offset in minutes
            int hours = offset / 60;
            int minutes = offset % 60;
            if( hours != 0 )
                newDate.roll( Calendar.HOUR, hours );
            if( minutes != 0 )
                newDate.roll( Calendar.MINUTE, minutes );
        }

        return newDate;
    }

    private static int getNumber( StringTokenizer parser )
                                  throws NumberFormatException,
                                         NoSuchElementException {
        return Integer.parseInt( parser.nextToken() );
    }

	public static String readStream(InputStream stream) throws IOException{
		int i, length = 0;
		StringBuffer s = null;
		char[] c = null;
		InputStreamReader isr = new InputStreamReader(stream);
		while(length != -1){
			length = stream.available();
			if(c == null) c = new char[length];
			isr.read(c, 0 , length);
			i = isr.read();
			if(i == -1){
				if(s == null) return String.copyValueOf(c);
				else break;
			}else{
				if(s == null) s = new StringBuffer();
				s.append(c, 0, length);
				s.append((char)i);
			}
		}
		return s.toString();
	}

}
