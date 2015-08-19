package com.openwave.mms.mm7.soap;

public class SOAPException extends Exception {

    private String string;

    public SOAPException( String string ) {
        this.string = string;
    }

    public String getMessage() { return string; }

}
