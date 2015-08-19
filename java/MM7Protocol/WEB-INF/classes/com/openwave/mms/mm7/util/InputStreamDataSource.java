package com.openwave.mms.mm7.util;

import javax.activation.DataSource;
import java.io.InputStream;
import java.io.OutputStream;
import java.io.IOException;

public class InputStreamDataSource implements DataSource {

    private InputStream stream;
    private String contentType;

    public InputStreamDataSource( InputStream stream, String contentType ) {
        this.stream = stream;
        this.contentType = contentType;
    }

    public String getContentType() { return contentType; }

    public InputStream getInputStream() throws IOException {
        return stream;
    }

    public OutputStream getOutputStream() throws IOException {
        return null;
    }

    public String getName() { return "name"; }
}
