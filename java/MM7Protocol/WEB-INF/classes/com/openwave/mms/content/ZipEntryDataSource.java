// EDITOR NOTE: Please set number of columns to 100 in ur editor.

package com.openwave.mms.content;

import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;

import java.util.zip.ZipFile;
import java.util.zip.ZipEntry;

import javax.activation.DataSource;
import javax.activation.FileTypeMap;

/**
 * The ZipEntryDataSource class implements a simple DataSource object
 * that encapsulates a file. It provides data typing services via
 * a FileTypeMap object. <p>
 *
 * <b>ZipEntryDataSource Typing Semantics</b><p>
 *
 * The ZipEntryDataSource class delegates data typing of files
 * to an object that is a subclass of the FileTypeMap class.
 * The <code>setFileTypeMap</code> method can be used to explicitly
 * set the FileTypeMap for an instance of ZipEntryDataSource. If no
 * FileTypeMap is set, the ZipEntryDataSource will call the 
 * getDefaultFileTypeMap method to get the system's default FileTypeMap.
 *
 * @see javax.activation.DataSource
 * @see javax.activation.FileTypeMap
 * @see javax.activation.MimetypesFileTypeMap
 */
final class ZipEntryDataSource implements DataSource {

    // keep track of original 'ref' passed in, non-null
    // one indicated which was passed in:
    private ZipFile _file = null;
    private ZipEntry _entry = null;
    private FileTypeMap typeMap = null;

    /**
     * Creates a ZipEntryDataSource from a ZipEntry object. <i>Note:
     * The file will not actually be opened until a method is
     * called that requires the file to be opened.</i>
     *
     * @param file The file
     */
    public ZipEntryDataSource(ZipFile file, ZipEntry entry) {
	_file = file;	// save the file Object...
        _entry = entry;
    }

    /**
     * This method returns an InputStream representing the
     * the data and will throw an IOException if it cannot
     * do so. This method returns a new
     * instance of InputStream with each invocation.
     *
     * @return an InputStream
     */
    public InputStream getInputStream() throws IOException {
	return _file.getInputStream(_entry);
    }

    /**
     * This method returns an OutputStream representing the
     * the data and will throw an IOException if it cannot
     * do so. This method returns a new instance of
     * OutputStream with each invocation.
     *
     * @return an OutputStream
     */
    public OutputStream getOutputStream() throws IOException {
        throw new UnsupportedOperationException();
    }

    /**
     * This method returns the MIME type of the data in the form of a string.
     * If there is no FileTypeMap explictly set, the ZipEntryDataSource 
     * calls the <code>getDefaultFileTypeMap</code> method on
     * FileTypeMap to acquire a default FileTypeMap. <i>Note: By
     * default, the FileTypeMap used will be a MimetypesFileTypeMap.</i>
     *
     * @return the MIME Type
     * @see javax.activation.FileTypeMap#getDefaultFileTypeMap
     */
    public String getContentType() {
	// check to see if the type map is null
	if (typeMap == null)
	    return FileTypeMap.getDefaultFileTypeMap().getContentType(_entry.getName());
	else
	    return typeMap.getContentType(_entry.getName());
    }

    /**
     * Return the <i>name</i> of the object. The ZipEntryDataSource
     * returns the file name of the object.
     *
     * @return The name of the object.
     * @see javax.activation.DataSource
     */
    public String getName() {
	return _file.getName();
    }

    /**
     * Set the FileTypeMap to use with the ZipEntryDataSource
     *
     * @param map The FileTypeMap for the object.
     */
    public void setFileTypeMap(FileTypeMap map) {
	typeMap = map;
    }
}
