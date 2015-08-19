package com.globalnet.standalone;

/*
 * SimpleSender.java
 *
 * Copyright (c) 2002 Openwave Systems Inc. All rights reserved.
 *
 * The copyright to the computer software herein is the property of
 * Openwave Systems Inc. The software may be used and/or copied only
 * with the written permission of Openwave Systems Inc. or in accordance
 * with the terms and conditions stipulated in the agreement/contract
 * under which the software has been supplied.
 *
 */

import javax.mail.internet.*;
import javax.mail.*;
import javax.activation.*;

import com.openwave.mms.content.Audio;
import com.openwave.mms.content.ContentException;
import com.openwave.mms.content.Factory;
import com.openwave.mms.content.Image;
import com.openwave.mms.content.Slide;
import com.openwave.mms.content.Text;
import com.openwave.mms.mm7.APIException;
import com.openwave.mms.mm7.AddressType;
import com.openwave.mms.mm7.FaultResponse;
import com.openwave.mms.mm7.Recipient;
import com.openwave.mms.mm7.RelayConnection;
import com.openwave.mms.mm7.Response;
import com.openwave.mms.mm7.SubmitRequest;
import com.openwave.mms.mm7.SubmitResponse;

import java.util.*;
import java.io.*;
import java.net.*;

public class SimpleSender {

    private static String sendMM(String mmscUrl, String userName, String password, String recipient, String textFile, String imageFile, String audioFile) {
    	String messageId = null;
        try {
            // create a connection to the mmsc
            RelayConnection conn = RelayConnection.createSender(mmscUrl, userName, password);

            // make a slide from the media files
            Slide slide = makeSlide(textFile, imageFile, audioFile);

            // now create a SubmitRequest object, use the connection object created earlier
            // to send it to the relay
            SubmitRequest request = createSubmitRequest(slide, userName, recipient, mmscUrl);

            // now send the request using the connection created above
            Response response = conn.sendRequest(request);
            
//            messageId = printResponse(response);
            conn.printRequest(request, null);
            
            
        }catch(APIException ae) {
            System.out.println("APIException: " + ae.getMessage());
        }catch(ContentException ce) {
            System.out.println("ContentException: " + ce.getMessage());
        }catch(MessagingException me) {
            System.out.println("MessagingException: " + me.getMessage());
        }catch(FileNotFoundException fnfe) {
            System.out.println("media file not found: " + fnfe.getMessage());
        }catch(MalformedURLException mue) {
            System.out.println("bad url " + mue.getMessage());
        }catch(IOException ioe) {
            System.out.println("IOException reading media files: " + ioe.getMessage());
        }

        return messageId;
    }

    private static Slide makeSlide(String textFile, String imageFile, String audioFile) throws FileNotFoundException, IOException, ContentException, UnsupportedEncodingException {

        // get a Slide instance from the factory
        Slide slide = Factory.getInstance().newSlide();

        // check and set those media files which were specified on the command line
        if( textFile != null ) {
            slide.setText(new Text(new File(textFile), null));
        }
        if (imageFile != null) {
        	System.out.println("image:" + imageFile);
            slide.setImage(new Image(new File(imageFile)));
        }
        if (audioFile != null) {
            slide.setAudio(new Audio(new File(audioFile)));
        }

        return slide;
    }

    private static SubmitRequest createSubmitRequest(Slide slide, String userName, String recipient, String mmscUrl) throws APIException, ContentException, MessagingException, IOException {

        // create the SubmitRequest object and use its methods to set different elements
        // of the request
        SubmitRequest request = new SubmitRequest();
        request.addRecipient(Recipient.Type.TO, AddressType.NUMBER, recipient);
        request.setVaspID("wazzup");
        
        if (allNumbers(userName)) {
            request.setSenderAddress(userName, AddressType.NUMBER);
        }else{
            request.setSenderAddress(userName + "@" + new URL(mmscUrl).getHost(), AddressType.EMAIL);
        }
        request.setSubject("WazzUp");

        // set the MM content
        List slides = new ArrayList();

        slides.add(slide);
        
        request.setContent(slides);

        return request;
    }

    private static String printResponse(Response response) {

    	String messageId = null;
    	if (response instanceof SubmitResponse) {
            SubmitResponse submitResponse = (SubmitResponse) response;

            // message id is the only important info in a submit response
            System.out.println("Message ID for the submitted message: " + submitResponse.getMessageID());
            messageId = submitResponse.getMessageID();
        } else {
            // sendRequest returns either SubmitResponse or FaultResponse.
            FaultResponse resp = ( FaultResponse ) response;
            System.out.println("Message Submission failed: " + resp.getStatusText() + "(" + resp.getStatusCode() + ")");

        }
        
        return messageId;
    }

 	// Main para testing directo
    public static void main(String args[]){
    	String messageId = enviarMMS("album.jpg","59899281412");
    	System.out.println("messageId:" + messageId);
    }

    public static String enviarMMS(String file, String recipient) {
        String userName = null;
        String password = null;
        String mmscUrl = null;
        String messageId = null;
        String audioFile = null;
        String imageFile = null;
        String textFile = null;
        
        String ext = file.substring(file.lastIndexOf("."));

        if (ext.equals(".mid") || ext.equals(".mp3") || ext.equals(".amr")) {
        	audioFile = file;
        }
        
        if (ext.equals(".jpg") || ext.equals(".gif")) {
        	imageFile = file;
        }
        
        if ((imageFile == null || imageFile.length() == 0) && (audioFile == null || audioFile.length() == 0)) {
            System.out.println("Atleast one media file must be specified");
        }
        
        if (recipient == null || recipient.length() == 0) {
            System.out.println("Recipient must be specified");
        }

        try {
            // read props file for mmscurl, username and password
            Properties props = new Properties();
            props.load( SimpleSender.class.getResourceAsStream("/resources/SimpleSender.properties"));
            mmscUrl = props.getProperty("mmscurl");
            userName = props.getProperty("username");
            password = props.getProperty("password");
            
        } catch( IOException ioe ) {
            System.out.println("IOException reading properties file: " + ioe.getMessage());
        }

        if (mmscUrl == null || mmscUrl.length() == 0) {
            System.out.println("URL must be specified in the properties file SimpleSender.properties");
        }

        if (userName == null || userName.length() == 0) {
            System.out.println("User name must be specified in the properties file SimpleSender.properties");
        }
          
        if (password == null || password.length() == 0) {
    	   System.out.println("Password must be specified in the properties file SimpleSender.properties");
     	}

        messageId = sendMM(mmscUrl, userName, password, recipient, textFile, imageFile, audioFile);
        
        return messageId;
    }

    private static boolean allNumbers( String str ) {
        for( int i = 0; i < str.length(); i++ ) {
            if( ! Character.isDigit( str.charAt( i ) ) ) {
                return false;
            }
        }

        return true;
    }
    
}
