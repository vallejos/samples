package com.openwave.mms.content;

import javax.activation.ActivationDataFlavor;
import javax.activation.DataContentHandler;
import javax.activation.DataSource;
import java.awt.datatransfer.DataFlavor;
import java.io.OutputStream;
import java.io.IOException;
import javax.mail.MessagingException;
import javax.mail.internet.MimeMultipart;


public class DRMContentHandler implements DataContentHandler {

    private ActivationDataFlavor myDF = new ActivationDataFlavor(
	    javax.mail.internet.MimeMultipart.class,
	    "application/vnd.oma.drm.message", 
	    "DRM Message" );

    /**
     * Returns the DataFlavors for this <code>DataContentHandler</code>.
     *
     * @return The DataFlavors
     */
    public DataFlavor[] getTransferDataFlavors() { // throws Exception;
	return new DataFlavor[] { myDF };
    }

    /**
     * Returns the Transfer Data of type DataFlavor from InputStream.
     *
     * @param df The DataFlavor
     * @param ds The data source corresponding to the data
     * @return A string object
     */
    public Object getTransferData( DataFlavor df, DataSource ds ) {
	// use myDF.equals to be sure to get ActivationDataFlavor.equals,
	// which properly ignores Content-Type parameters in comparison
	if( myDF.equals( df ) )
	    return getContent( ds );
	else
	    return null;
    }
    
    /**
     * Returns the content.
     */
    public Object getContent( DataSource ds ) {
	try {
	    return new MimeMultipart( ds ); 
	} catch( MessagingException e ) {
	    return null;
	}
    }
    
    /**
     * Writes the object to the output stream, using the specific MIME type.
     */
    public void writeTo( Object obj, String mimeType, OutputStream os ) 
			 throws IOException {
	if( obj instanceof MimeMultipart ) {
	    try {
		((MimeMultipart) obj ).writeTo( os );
	    } catch( MessagingException e ) {
		throw new IOException( e.toString() );
	    }
	}
    }
}

