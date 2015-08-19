/**
 * Copyright (c) 2002-2003 Openwave Systems Inc. All rights reserved.
 *
 * The copyright to the computer software herein is the property of
 * Openwave Systems Inc. The software may be used and/or copied only
 * with the written permission of Openwave Systems Inc. or in accordance
 * with the terms and conditions stipulated in the agreement/contract
 * under which the software has been supplied.
 *
 * $Id: MultimediaContentImpl.java,v 1.1 2007/02/20 16:01:46 cvsuser Exp $
 */
package com.openwave.mms.content;

import java.awt.Dimension;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FilenameFilter;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.StringWriter;

import javax.mail.BodyPart;
import javax.mail.Header;
import javax.mail.MessagingException;
import javax.mail.Multipart;
import javax.mail.Session;
import javax.mail.internet.ContentType;
import javax.mail.internet.MimeBodyPart;
import javax.mail.internet.MimeMessage;
import javax.mail.internet.MimeMultipart;
import javax.mail.internet.MimeUtility;
import javax.mail.internet.ParseException;

import javax.activation.DataHandler;
import javax.activation.DataSource;
import javax.activation.FileDataSource;

import java.util.Enumeration;
import java.util.List;
import java.util.ListIterator;
import java.util.LinkedList;
import java.util.Properties;
import java.util.Random;
import java.util.zip.ZipFile;
import java.util.zip.ZipEntry;
import java.util.zip.ZipException;

import org.apache.log4j.Level;
import org.apache.log4j.Logger;
import org.exolab.castor.xml.MarshalException;
import org.exolab.castor.xml.ValidationException;

import com.openwave.mms.content.smil.Body;
import com.openwave.mms.content.smil.Head;
import com.openwave.mms.content.smil.Img;
import com.openwave.mms.content.smil.Layout;
import com.openwave.mms.content.smil.Par;
import com.openwave.mms.content.smil.Smil;
import com.openwave.mms.content.smil.Region;
import com.openwave.mms.content.smil.RootLayout;
import com.openwave.mms.content.smil.types.RegionTypeIdType;

/**
 * This package is a private class that implements the MultimediaContent interface. Each
 * object of this type consists of a list of Slide objects that corresponds
 * to a "par" element in SMIL. Each Slide object in turn contains the actual
 * multimedia objects like Video, Image, Audio and Text. A MultimediaContent
 * object also contains a Template object which specifies the coordinates
 * of regions on screen where the different multimedia objects (only Text, 
 * Video and Image, in this case) are to be rendered.
 */
final class MultimediaContentImpl implements MultimediaContent {

    /**
     * The default constructor that can be used to construct a MultimediaContentImpl
     * object from scratch.
     */
    public MultimediaContentImpl() {
        changed = true;
        slides = new LinkedList();
        viewportSize = new Dimension();
        smilBased = true;
    }

    /**
     * This constructor tries to construct a MultimediaContentImpl object by reading
     * the input stream provided. It looks for the MIME part that contains the SMIL
     * description of the content and constructs the slides accordingly. If a SMIL
     * part is not found, it keeps the content as a multipart and does not
     * create any slides. Applications should use <code>isSmilBased</code> to check
     * whether the content is SMIL-based and call <code>getContent</code> if it returns
     * false. If <code>isSmilBased</code> returns true, you can use the methods
     * that allow you to access the slides.
     *
     * @param inputStream The input stream from which to read the multimedia content.
     * @exception IOException The input stream cannot be read.
     * @exception ContentException The stream does not contain a SMIL part
     *            or there is a MessagingException while dealing with the MIME body parts.
     * @exception InvalidSmilException A subclass of ContentException is thrown if the 
     *            SMIL cannot be parsed.
     */
    public MultimediaContentImpl(InputStream inputStream) throws ContentException, IOException {
        viewportSize = new Dimension();
        slides = new LinkedList();
        smilBased = false;

        try {
            MimeMessage message = new MimeMessage(Session.getDefaultInstance(new Properties()), inputStream);
            content = message.getContent();

            if (content instanceof MimeMultipart) {
                createFromMultipart();
            }else{
                content = new MimeBodyPart();
                Enumeration headers = message.getAllHeaders();
                while (headers.hasMoreElements()) {
                    Header header = (Header) headers.nextElement();
                    ((MimeBodyPart) content).addHeader(header.getName(), header.getValue());
                }

                ((MimeBodyPart) content).setContent(message.getContent(), message.getContentType());
            }

        }catch(MessagingException mme) {
            throw new ContentException("messaging-exception", mme);
        }
    }

    /**
     * This constructor tries to construct a MultimediaContentImpl object from
     * the MimeMultipart provided. It looks for the MIME part that contains the SMIL
     * description of the content and constructs the slides accordingly. If a SMIL
     * part is not found, it keeps the content as a multipart and does not
     * create any slides. Applications should use <code>isSmilBased</code> to check
     * whether the content is SMIL-based and call <code>getContent</code> if it returns
     * false. If <code>isSmilBased</code> returns true then you can use the methods
     * that allow you to access the slides.
     *
     * @param multipart The object from which to construct the multimedia content.
     * @exception IOException The multipart cannot be read.
     * @exception ContentException The multipart does not contain a SMIL part
     *            or there is a MessagingException dealing with the MIME body parts.
     * @exception IllegalContentTypeException A subclass of ContentException is thrown
     *            if the content-type header in the MIME input stream is not multipart/
     *            related or if it cannot be parsed.
     * @exception InvalidSmilException A subclass of ContentException is thrown if the 
     *            SMIL cannot be parsed.
     */
    public MultimediaContentImpl(MimeMultipart multipart) throws ContentException, IOException {
        viewportSize = new Dimension();
        slides = new LinkedList();
        content = multipart;
        smilBased = false;
        createFromMultipart();
    }

    /**
     * This constructor takes a java.io.File as input. The file could be a directory, a SMIL 
     * file, or a zip file. If it is a directory, it lists the files in the
     * directory and picks the first file with an extension of .smil and contructs
     * the MultimediaContentImpl object from it. If it is a SMIL file, it constructs
     * the MultimediaContentImpl object as described by the SMIL file. If it is a zip
     * file, it treats it as a directory and constructs the MultimediaContentImpl
     * object accordingly.
     *
     * @param file The file as a File object.
     * @exception FileNotFoundException The file, or any other media file
     *            referenced by the SMIL file, is not found.
     * @exception IOException There is an error reading any of the files.
     * @exception ContentException The directory/zip file does not contain
     *            a SMIL file, there is a MessagingException dealing with the MIME
     *            body parts, or the file is neither a directory nor a zip file nor
     *            a SMIL file.
     * @exception InvalidSmilException A subclass of ContentException is thrown if the 
     *            SMIL cannot be parsed.
     *
     */
    public MultimediaContentImpl(File file) throws ContentException, IOException {
        viewportSize = new Dimension();
        changed = true;
        slides = new LinkedList();
        smilBased = true;

        try {
            if (file.isDirectory()) {

                String[] smilFiles = file.list(new FilenameFilter(){
								                    	public boolean accept(File dir, String name) {
								                    		return name.endsWith(".smil");
								                    	}
                									}
                							   );

                if (smilFiles == null || smilFiles.length == 0 || smilFiles[0] == null || smilFiles[0].length() == 0) {
                    throw new SmilNotFoundException("no-smil-directory");
                }

                createFromSmilFile(new File(file, smilFiles[0]));

            }else if (file.getName().endsWith(".zip")) {
                ZipFile zipFile = new ZipFile(file);
                createFromZipFile(zipFile);

            }else{
                if (!file.getName().endsWith(".smil")) {
                    throw new ContentException("file-must-be-directory-zip-smil");
                }

                createFromSmilFile(file);
            }
            
        }catch(ValidationException ve) {
            throw new InvalidSmilException("smil-validation-failed", ve);
        }catch(MarshalException me) {
            throw new InvalidSmilException("smil-parsing-failed", me);
        }catch(MessagingException mme) {
            throw new ContentException("messaging-exception", mme);
        }
    }

    /**
     * This method is used to check if the content contained in the object
     * has a SMIL description associated with it. When the object is constructed
     * using the constructors which take the input stream or a multipart,
     * this method tells you whether the content has a SMIL part.
     *
     * @return True if the content is smil based, false otherwise.
     */
    public boolean isSmilBased() {
        return smilBased;
    }

    /**
     * Add this slide to the list of slides in the object.
     *
     * @param slide The slide to be added.
     */
    public void addSlide(Slide slide) {
        changed = true;
        slides.add(slide);
    }

    /**
     * Add this slide to the list of slides in the object.
     *
     * @param slide The slide to be added.
     * @param index The index at which to add the slide.
     */
    public void addSlide(Slide slide, int index) {
        changed = true;
        slides.add(index, slide);
    }

    /**
     * Remove a slide given its position in the list.
     *
     * @param slideNumber The position of the slide in the list.
     * @exception IndexOutOfBoundsException There are not that many slides
     *            in this object.
     */
    public void removeSlide(int slideNumber) throws IndexOutOfBoundsException {
        slides.remove(slideNumber);
        changed = true;
    }
 
    /**
     * Remove a slide.
     *
     * @param slide The slide to be removed.
     */
    public void removeSlide(Slide slide) {
        slides.remove(slide);
        changed = true;
    }
 
    /**
     * Creates a new Slide object, adds it to the list of slides in the object,
     * and returns the slide.
     */
    public Slide newSlide() {
        changed = true;
        Slide slide = SlideFactory.getInstance().newSlide();
        slides.add(slide);
        return slide;
    }

    /**
     * Creates a new Slide object, adds it to the list of slides in the object,
     * and returns the slide.
     *
     * @param index The index at which to add the slide.
     */
    public Slide newSlide(int index) {
        changed = true;
        Slide slide = SlideFactory.getInstance().newSlide();
        slides.add(index, slide);
        return slide;
    }

    /**
     * This method is used to clear the list of slides contained in the object.
     */
    public void clear() {
        changed = true;
        slides.clear();
    }

    /**
     * This method can be used to write the contents of the object to an output
     * stream. It generates the SMIL corresponding to its contents, creates a
     * MIME multipart message with the SMIL and the media objects, and writes it
     * to the output stream.
     *
     * @param outputStream The output stream to write to.
     * @exception IOException There is an error writing output to the stream.
     * @exception ContentException There is a MessagingException dealing with the MIME
     *            body parts.
     * @exception InvalidSmilException A subclass of ContentException is thrown if the 
     *            SMIL cannot be synthesized.
     */
    public void writeTo(OutputStream outputStream) throws ContentException, IOException {
        try {
            if (smilBased && changed) {
                createMultipart();
            }

            if (smilBased) {
                MimeMultipart multipart = (MimeMultipart) content;

                String contentType = multipart.getContentType() +
                                     ";\r\n\tstart=\"<mms.smil>\"" +
                                     ";\r\n\ttype=\"application/smil\"";
                
                MimeMessage message = new MimeMessage( Session.getDefaultInstance( new Properties() ) );
                message.setContent( multipart, contentType );
                message.writeTo( outputStream );
                
            }else if (content instanceof MimeBodyPart) {
                ((MimeBodyPart) content).writeTo(outputStream);
            }else if (content instanceof MimeMultipart) {
                MimeMessage message = new MimeMessage(Session.getDefaultInstance(new Properties()));
                message.setContent(((MimeMultipart) content));
                message.writeTo(outputStream);
            }
            
        }catch(ValidationException ve) {
            throw new InvalidSmilException("smil-validation-failed", ve);
        }catch(MarshalException me) {
            throw new InvalidSmilException("smil-creation-failed", me);
        }catch(MessagingException mme) {
        	throw new ContentException("messaging-exception", mme);
        }
    }

    /**
     * This method returns the content of the object as a MimeBodyPart or
     * MimeMultipart. If the object was created from a MimeBodyPart or a
     * MimeMultipart, it returns the same. If the object was created from an
     * input stream containing a mime formatted message, it returns a MimeBodyPart
     * if the input stream contained a single part and MimeMultipart if the stream
     * contained a multipart message. If this object was created using any other
     * constructors, it always returns a MimeMultipart containing the SMIL and
     * the content parts. 
     *
     * @return A MimeMultipart object containing the SMIL and the media objects
     *         or a MimeBodyPart.
     * @exception MessagingException The MimeMultipart object cannot be created.
     * @exception InvalidSmilException A subclass of ContentException is thrown if the 
     *            SMIL cannot be parsed/synthesized.
     */
    public Object getContent() throws ContentException, MessagingException {
        try {
            if (smilBased && changed) {
                createMultipart();
            }
            
            return content;
            
        }catch(ValidationException ve) {
            throw new InvalidSmilException("smil-validation-failed", ve);
        }catch(MarshalException me) {
            throw new InvalidSmilException("smil-creation-failed", me);
        }
    }

    /**
     * This method returns the slides contained in the object as a List.
     *
     * @return The list of Slide objects.
     */
    public List getSlides() {
        return slides;
    }

    /**
     * This method can be used to set the slides of the object.
     *
     * @param slides A list of Slide objects. The existing slides will be replaced
     *        by this list.
     */
    public void setSlides(List slides) {
        changed = true;
        this.slides = slides;
    }

    /**
     * This method returns the slide indexed by an integer.
     *
     * @param slideNumber The index of the slide in the list.
     * @return The Slide object indexed by the slideNumber.
     * @exception IndexOutOfBoundsException The list does not contain that many elements.
     */
    public Slide getSlide(int slideNumber) throws IndexOutOfBoundsException {
        return (Slide) slides.get(slideNumber);
    }

    /**
     * This method returns the number of slides in the object.
     *
     * @return The number of slides in the object.
     */
    public int getNumSlides() {
        return slides.size();
    }

    /**
     * This method is used to set the template for the slides in the object.
     *
     * @param template The template to be used for the slides in the object.
     */
    public void setTemplate(Template template) {
        this.template = template;
    }

    /**
     * This method returns the Template associated with the slides in the object.
     *
     * @return The template associated with the slides in the object.
     */
    public Template getTemplate() {
        return template;
    }

    /**
     * This method is used to set the size of the viewport in which the SMIL
     * presentation is rendered.
     *
     * @param viewportSize The size of the viewport.
     */
    public void setViewportSize(Dimension viewportSize) {
        this.viewportSize = viewportSize;
    }

    /**
     * This method returns the viewport size.
     *
     * @return The size of the viewport.
     */
    public Dimension getViewportSize() {
        return viewportSize;
    }

    /**
     * This method converts the content into a JavaMail MimeMultipart object.
     * It also initializes the multipart member of this class and updates
     * the changed flag to false.
     */
    private void createMultipart() throws MessagingException, ValidationException, MarshalException {
        changed = false;
        
        if (template == null) {
            template = TemplateFactory.getInstance().getTemplate(null);
        }
        
        MimeMultipart multipart = new MimeMultipart("related");

        Smil smil = new Smil();
        Body body = new Body();
        Head head = new Head();
        Layout layout = new Layout();
        RootLayout rootLayout = new RootLayout();
        rootLayout.setWidth( "100%" );
        rootLayout.setHeight( "100%" );
        
        if (viewportSize != null) {
            if (viewportSize.getWidth() > 0) {
                rootLayout.setWidth(String.valueOf(viewportSize.getWidth()));
            }

            if (viewportSize.getHeight() > 0) {
                rootLayout.setHeight(String.valueOf(viewportSize.getHeight()));
            }
        }
        
        layout.setRootLayout(rootLayout);
        head.setLayout(layout);
        smil.setHead(head);
        smil.setBody( body );

        List fileList = new LinkedList();
        ListIterator iterator = slides.listIterator();
        
        while (iterator.hasNext()) {
            Slide slide = (Slide) iterator.next();
            Par par = new Par();
            int duration = slide.getDuration();
            if (duration > 0) {
                par.setDur(String.valueOf(duration) + "ms");
            }
            body.addPar(par);

            Text text = slide.getText();
            if (text != null) {
                // check if there is a file name associated with the part
                String textContentID = addMediaToMultipart(multipart, text);
                com.openwave.mms.content.smil.Text textElement = new com.openwave.mms.content.smil.Text();
                textElement.setSrc("cid:" + textContentID);
                
                int begin = text.getBegin();
                if (begin > 0) {
                    textElement.setBegin(String.valueOf(begin));
                }
                
                int end = text.getEnd();
                if (end > 0) {
                    textElement.setEnd(String.valueOf(end));
                }
                
                textElement.setAlt(text.getAlt());
                RegionAttributes textAttributes = template.getTextAttributes();
                addRegion("Text", layout, textAttributes);
                textElement.setRegion("Text");
                par.setText(textElement);
            }

            Video video = slide.getVideo();
            if( video != null ) {
                // check if there is a file name associated with the part
                String videoContentID = addMediaToMultipart( multipart, video );
                com.openwave.mms.content.smil.Video videoElement = new com.openwave.mms.content.smil.Video();
                videoElement.setSrc("cid:" + videoContentID);
                
                int begin = video.getBegin();
                if (begin > 0) {
                    videoElement.setBegin(String.valueOf(begin));
                }
                
                int end = video.getEnd();
                if (end > 0) {
                    videoElement.setEnd(String.valueOf(end));
                }
                
                videoElement.setAlt( video.getAlt() );
                //video and image share the Image smil region
                RegionAttributes imageAttributes = template.getImageAttributes();
                addRegion("Image", layout, imageAttributes);
                videoElement.setRegion( "Image" );
                par.setVideo( videoElement );
            }

            Image image = slide.getImage();
            if (image != null) {
                // check if there is a file name associated with the part
                String imageContentID = addMediaToMultipart(multipart, image);
                Img imgElement = new Img();
                imgElement.setSrc("cid:" + imageContentID);

                int begin = image.getBegin();
                if (begin > 0) {
                    imgElement.setBegin(String.valueOf(begin));
                }
                
                int end = image.getEnd();
                if (end > 0) {
                    imgElement.setEnd(String.valueOf(end));
                }
                
                imgElement.setAlt(image.getAlt());
                RegionAttributes imageAttributes = template.getImageAttributes();
                addRegion("Image", layout, imageAttributes);
                imgElement.setRegion("Image");
                par.setImg(imgElement);
            }

            Audio audio = slide.getAudio();
            if( audio != null ) {
                // check if there is a file name associated with the part
                String audioContentID = addMediaToMultipart(multipart, audio);
                com.openwave.mms.content.smil.Audio audioElement = new com.openwave.mms.content.smil.Audio();
                audioElement.setSrc("cid:" + audioContentID);

                int begin = audio.getBegin();
                if (begin > 0) {
                    audioElement.setBegin(String.valueOf(begin));
                }
                
                int end = audio.getEnd();
                if (end > 0) {
                    audioElement.setEnd(String.valueOf(end));
                }
                
                audioElement.setAlt(audio.getAlt());
                par.setAudio(audioElement);
            }
        }

        StringWriter writer = new StringWriter();
        smil.marshal(writer);
        MimeBodyPart smilPart = new MimeBodyPart();
        smilPart.setText(writer.toString());
        smilPart.setHeader("Content-Type", "application/smil");
        String smilContentID = "<mms.smil>";
        smilPart.setHeader("Content-ID", smilContentID);
        multipart.addBodyPart(smilPart);
        content = multipart;
    }

    private BodyPart searchForSmilPart(MimeMultipart multipart) throws MessagingException {
    	for (int i = 0; i < multipart.getCount(); i++) {
            BodyPart part = multipart.getBodyPart(i);
            try {
                ContentType contentType = new ContentType(part.getContentType());
                if (contentType.getBaseType().equals("application/smil")) {
                    return part;
                }
            } catch(ParseException pe) {
                return null;
            }
        }
        return null;
    }

    private void createSlidesFromSmil(BodyPart smilPart, MimeMultipart multipart) throws IOException, MarshalException, ValidationException, MessagingException, ContentException {
        Smil smil = Smil.unmarshal( new InputStreamReader( smilPart.getInputStream() ) );

        createTemplate(smil);

        for (int i = 0; i < smil.getBody().getParCount(); i++) {
            Par par = smil.getBody().getPar(i);

            Slide slide = SlideFactory.getInstance().newSlide();
            slide.setDuration( par.getDur());

            com.openwave.mms.content.smil.Text text = par.getText();
            if (text != null) {
                BodyPart textPart = getReferencedPart(text.getSrc(), multipart);
                if (textPart != null) {
                    addText(slide, new Text(textPart), text);
                }
            }

            com.openwave.mms.content.smil.Audio audio = par.getAudio();
            if (audio != null) {
                BodyPart audioPart = getReferencedPart(audio.getSrc(), multipart);
                if (audioPart != null) {
                    addAudio(slide, new Audio(audioPart), audio);
                }
            }

            com.openwave.mms.content.smil.Img image = par.getImg();
            if (image != null) {
                BodyPart imagePart = getReferencedPart(image.getSrc(), multipart);
                if (imagePart != null) {
                    addImage(slide, new Image(imagePart), image);
                }
            }

            com.openwave.mms.content.smil.Video video = par.getVideo();
            if (video != null) {
                BodyPart videoPart = getReferencedPart(video.getSrc(), multipart);
                if (videoPart != null) {
                    addVideo(slide, new Video(videoPart), video);
                }
            }

            slides.add(slide);
        }
    }

    private void createFromSmilFile(File smilFile) throws FileNotFoundException, ContentException, MessagingException, MarshalException, ValidationException, IOException {
        Smil smil = Smil.unmarshal(new FileReader(smilFile));

        createTemplate(smil);

        for (int i = 0; i < smil.getBody().getParCount(); i++) {
            Par par = smil.getBody().getPar(i);

            Slide slide = SlideFactory.getInstance().newSlide();
            slide.setDuration(par.getDur());

            com.openwave.mms.content.smil.Text text = par.getText();
            if (text != null) {
                Text textContent = new Text(new File(smilFile.getParent(), text.getSrc()), MimeUtility.mimeCharset(MimeUtility.getDefaultJavaCharset()));
                addText(slide, textContent, text);
            }

            com.openwave.mms.content.smil.Audio audio = par.getAudio();
            if (audio != null) {
                Audio audioContent = new Audio(new File(smilFile.getParent(), audio.getSrc()));
                addAudio(slide, audioContent, audio);
            }

            com.openwave.mms.content.smil.Img image = par.getImg();
            if (image != null) {
                Image imageContent = new Image(new File(smilFile.getParent(), image.getSrc()));
                addImage(slide, imageContent, image);
            }

            com.openwave.mms.content.smil.Video video = par.getVideo();
            if (video != null) {
                Video videoContent = new Video(new File(smilFile.getParent(), video.getSrc()));
                addVideo(slide, videoContent, video);
            }

            slides.add(slide);
        }
    }

    private void createFromZipFile(ZipFile zipFile) throws IOException, MessagingException, MarshalException, ValidationException, ContentException, ZipException {
        ZipEntry smilFile = getSmilFileFromZip(zipFile);

        if (smilFile == null) {
            throw new SmilNotFoundException("no-smil-zip");
        }

        Smil smil = Smil.unmarshal(new InputStreamReader(zipFile.getInputStream(smilFile)));

        createTemplate(smil);

        for (int i = 0; i < smil.getBody().getParCount(); i++) {
            Par par = smil.getBody().getPar(i);

            Slide slide = SlideFactory.getInstance().newSlide();
            slide.setDuration(par.getDur());

            com.openwave.mms.content.smil.Text text = par.getText();
            if (text != null) {
                DataHandler handler = getZipEntryDataHandler(zipFile, text.getSrc());
                Text textPart = new Text();
                textPart.setDataHandler(handler);
                textPart.setFileName(text.getSrc());
                addText(slide, textPart, text);
            }

            com.openwave.mms.content.smil.Audio audio = par.getAudio();
            if (audio != null) {
                DataHandler handler = getZipEntryDataHandler(zipFile, audio.getSrc());
                Audio audioPart = new Audio();
                audioPart.setDataHandler(handler);
                audioPart.setFileName(audio.getSrc());
                addAudio(slide, audioPart, audio);
            }

            com.openwave.mms.content.smil.Img image = par.getImg();
            if (image != null) {
                DataHandler handler = getZipEntryDataHandler(zipFile, image.getSrc());
                Image imagePart = new Image();
                imagePart.setDataHandler(handler);
                imagePart.setFileName(image.getSrc());
                addImage(slide, imagePart, image);
            }

            com.openwave.mms.content.smil.Video video = par.getVideo();
            if (video != null) {
                DataHandler handler = getZipEntryDataHandler(zipFile, video.getSrc());
                Video videoPart = new Video();
                videoPart.setDataHandler(handler);
                videoPart.setFileName(video.getSrc());
                addVideo(slide, videoPart, video);
            }

            slides.add(slide);
        }
    }

    private String stripCid(String cid) {
        if (cid.substring(0, 4).equalsIgnoreCase("cid:")) {
            return cid.substring(4);
        }else{
        	return cid;
        }
    }

    private void addRegion(String regionName, Layout layout, RegionAttributes regionAttributes) {

    	Enumeration regions = layout.enumerateRegion();
        while (regions.hasMoreElements()) {
            Region region = (Region) regions.nextElement();
            if (regionAttributes != null){
	            if (regionAttributes.equals(region)) {
	                return;
	            }
            }
        }
        
        // existing region not found, create new one
        Region region = new Region();
        region.setId(RegionTypeIdType.valueOf(regionName));

        if (regionAttributes != null){
	        IntOrPercent top = regionAttributes.getTop();
	        if( top != null ) {
	            region.setTop( top.toString() );
	        }
	        IntOrPercent left = regionAttributes.getLeft();
	        if( left != null ) {
	            region.setLeft( left.toString() );
	        }
	        IntOrPercent width = regionAttributes.getWidth();
	        if( width != null ) {
	            region.setWidth( width.toString() );
	        }
	        IntOrPercent height = regionAttributes.getHeight();
	        if( height != null ) {
	            region.setHeight( height.toString() );
	        }
	        Fit fit = regionAttributes.getFit();
	        if( fit != null ) {
	            region.setFit( fit.toString() );
	        }
        }else{
            region.setTop("");
            region.setLeft("");
            region.setWidth("");
            region.setHeight("");
            region.setFit("");
        }

        layout.addRegion(region);
    }

    private ZipEntry getSmilFileFromZip(ZipFile zipFile) {
    	Enumeration files = zipFile.entries();
        while (files.hasMoreElements()) {
            ZipEntry file = (ZipEntry) files.nextElement();
            if (file.getName().endsWith(".smil")) {
                return file;
            }
        }
        return null;
    }

    private DataHandler getZipEntryDataHandler(ZipFile zipFile, String entry) {
        return new DataHandler(new ZipEntryDataSource(zipFile, zipFile.getEntry(entry)));
    }

    private static String generateContentID() {
        StringBuffer buffer = new StringBuffer();
        Random random = new Random();
        for (int i = 0; i < 16; i++) {
            int nextInt = random.nextInt(16);
            buffer.append(Integer.toHexString(random.nextInt(16)));
        }

        return buffer.toString();
    }

    private void createFromMultipart() throws ContentException, IOException {
        try {
            MimeMultipart multipart = (MimeMultipart) content;
            ContentType contentType = new ContentType(multipart.getContentType());

            String start = contentType.getParameter("start");

            if (start != null && start.length() > 0) {
                BodyPart smilPart = multipart.getBodyPart(start);
                
                if (smilPart == null || ! smilPart.getContentType().startsWith("application/smil")) {
                    smilPart = searchForSmilPart(multipart);
                }

                if (smilPart != null) {
                    smilBased = true;
                    changed = true;
                    createSlidesFromSmil(smilPart, multipart);
                }

            }else{
                // no start attribute in content type. just get all the parts and
                // look for smil
                BodyPart smilPart = searchForSmilPart(multipart);
                if (smilPart != null) {
                    smilBased = true;
                    changed = true;
                    createSlidesFromSmil(smilPart, multipart);
                }
            }
            
        }catch(ValidationException ve) {
            throw new InvalidSmilException("smil-validation-failed", ve);
        }catch(MarshalException me) {
            throw new InvalidSmilException("smil-parsing-failed", me);
        }catch(ParseException pe) {
            throw new IllegalContentTypeException("cannot-parse-content-type", pe);
        }catch(MessagingException mme) {
            throw new ContentException("messaging-exception", mme);
        }
    }

    /**
     * This function checks if the media object is already in the multipart
     * and adds it if it isn't. This check is necessary to make sure that we don't
     * add the same content twice when it is referenced by two slides.
     */
    private static String addMediaToMultipart(MimeMultipart multipart, MediaObject media) throws MessagingException {
        String contentId = getContentID(media);
        BodyPart part = multipart.getBodyPart("<" + contentId + ">");
        if (part != null) {
            contentId = generateContentID();
            media.setContentID("<" + contentId + ">");
        }

        // check if this media object is forward locked
        if (media.getForwardLock() == true) {
            multipart.addBodyPart(media.drmEncode());
        }else{
            multipart.addBodyPart(media);
        }

        return contentId;
    }

    private static String getContentID(MediaObject media) throws MessagingException {
        String contentID = media.getContentID();
        
        if (contentID == null || contentID.length() == 0) {
            contentID = media.getFileName();
            if (contentID == null || contentID.length() == 0) {
                contentID = generateContentID();
            }
            media.setHeader("Content-ID", "<" + contentID + ">");

        }else{
            // remove the leading and trailing angle brackets
            contentID = contentID.substring(1, contentID.length() - 1);
        }

        return contentID;
    }

    private BodyPart getReferencedPart(String src, MimeMultipart multipart) throws MessagingException { 
    	BodyPart part = null; 
        
    	if (src.startsWith("cid:")) { 
            String contentId = stripCid(src); 
            try {
            	part = multipart.getBodyPart("<" + contentId + ">"); 
            }catch(MessagingException me) {
                //ignore, this is most likely due to bad smil
                //or content being stripped because of drm
                //controls on the MMSC.
            }
        }else{ 
            int count = multipart.getCount(); 
            for (int i = 0; (i < count) && (part == null); i++) { 
                MimeBodyPart aPart = (MimeBodyPart) multipart.getBodyPart(i); 
                String contentLocation = aPart.getHeader("Content-Location", null); 
                if ((contentLocation != null) && (contentLocation.equals(src))) { 
                    part = aPart; 
                } 
            } 
        } 

        if (part == null) {
            if (logger.isEnabledFor(Level.WARN)) {
                logger.warn("media object [" + src + "] not found");
            }
        }

        return part; 
    }

    private void setViewportSizeFromSmil(RootLayout rootLayout) {
        int height = 0;
        int width = 0;

        String rootWidth = rootLayout.getWidth();
        if (rootWidth != null) {
            try {
                width = Integer.parseInt(rootWidth);
            }catch(NumberFormatException nfe) {
                //ignore
            }
        }

        String rootHeight = rootLayout.getHeight();
        if (rootHeight != null) {
            try {
                height = Integer.parseInt(rootHeight);
            }catch(NumberFormatException nfe) {
                //ignore 
            }
        }
        viewportSize.setSize(width, height);
    }

    private void createTemplate(Smil smil) {
        // set the template
        RegionAttributes textAttributes = null;
        RegionAttributes imageAttributes = null;

        Layout layout = smil.getHead().getLayout();
        setViewportSizeFromSmil(layout.getRootLayout());
        Enumeration regions = layout.enumerateRegion();

        while (regions.hasMoreElements()) {
            Region region = (Region) regions.nextElement();
            if (region.getId() == RegionTypeIdType.TEXT) {
                textAttributes = new RegionAttributes(region);
            }else if(region.getId() == RegionTypeIdType.IMAGE) {
                imageAttributes = new RegionAttributes(region);
            }
        }

        template = TemplateFactory.getInstance().newTemplate(null, textAttributes, imageAttributes);
    }

    private void addText(Slide slide, Text textContent, com.openwave.mms.content.smil.Text text) {
        textContent.setBegin(text.getBegin());
        textContent.setEnd(text.getEnd());
        textContent.setAlt(text.getAlt());
        slide.setText(textContent);
    }

    private void addAudio(Slide slide, Audio audioContent, com.openwave.mms.content.smil.Audio audio) throws ContentException {
        
    	audioContent.setBegin(audio.getBegin());
        audioContent.setEnd(audio.getEnd());
        audioContent.setAlt(audio.getAlt());

        try {
            slide.setAudio(audioContent);
        }catch(IllegalStateException e) {
            throw new ContentException("illegal-mms-smil", e);
        }
    }

    private void addImage(Slide slide, Image imageContent, com.openwave.mms.content.smil.Img image) throws ContentException {

    	imageContent.setBegin(image.getBegin());
        imageContent.setEnd(image.getEnd());
        imageContent.setAlt(image.getAlt());
        
        try {
            slide.setImage(imageContent);
        }catch(IllegalStateException e) {
            throw new ContentException("illegal-mms-smil", e);
        }
    }

    private void addVideo(Slide slide, Video videoContent, com.openwave.mms.content.smil.Video video) throws ContentException {
    	
        videoContent.setBegin(video.getBegin());
        videoContent.setEnd(video.getEnd());
        videoContent.setAlt(video.getAlt());
        
        try {
            slide.setVideo(videoContent);
        }catch(IllegalStateException e) {
            throw new ContentException("illegal-mms-smil", e);
        }
    }

    private List slides;
    private Object content;
    private boolean changed;
    private Template template;
    private Dimension viewportSize;
    private boolean smilBased;

    private static final Logger logger = Logger.getLogger( MultimediaContentImpl.class );

}
