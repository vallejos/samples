package com.openwave.mms.mm7.soap;

public class SOAPQName {
    private String prefix;
    private String name;
    private String uri;

    public SOAPQName() {
        prefix = null;
        name = null;
        uri = null;
    }

    public SOAPQName( String name, String prefix, String uri ) {
        this.prefix = prefix;
        this.name = name;
        this.uri = uri;
    }

    public SOAPQName( String name ) {
        this.name = name;
        prefix = null;
        uri = null;
    }

    public String getPrefix() { return prefix; }
    public void setPrefix(String prefix) { this.prefix = prefix; }
    public String getName() { return name; }
    public void setName(String name) { this.name = name; }
    public String getUri() { return uri; }
    public void setUri(String uri) { this.uri = uri; }
}
