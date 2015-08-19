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

import com.openwave.mms.mm7.*;
import com.openwave.mms.content.*;
import javax.mail.internet.*;
import javax.mail.*;
import javax.activation.*;
import java.util.*;
import java.io.*;
import java.net.*;

public class SimpleSenderBack {

    private static void sendMM( String mmscUrl, String userName,
                                String password, String recipient,
                                String textFile, String imageFile,
                                String audioFile ) {

        try {
            // create a connection to the mmsc
            RelayConnection conn = RelayConnection.createSender( mmscUrl, userName, password );

            // make a slide from the media files
            Slide slide = makeSlide( textFile, imageFile, audioFile );

            // now create a SubmitRequest object, use the connection object created earlier
            // to send it to the relay
            SubmitRequest request = createSubmitRequest( slide, userName, recipient, mmscUrl );

            // now send the request using the connection created above
            Response response = conn.sendRequest( request );
            printResponse( response );
        } catch( APIException ae ) {
            System.out.println( "APIException: " + ae.getMessage() );
            System.exit( 1 );
        } catch( ContentException ce ) {
            System.out.println( "ContentException: " + ce.getMessage() );
            System.exit( 1 );
        } catch( MessagingException me ) {
            System.out.println( "MessagingException: " + me.getMessage() );
            System.exit( 1 );
        } catch( FileNotFoundException fnfe ) {
            System.out.println( "media file not found: " + fnfe.getMessage() );
            System.exit( 1 );
        } catch( MalformedURLException mue ) {
            System.out.println( "bad url " + mue.getMessage() );
            System.exit( 1 );
        } catch( IOException ioe ) {
            System.out.println( "IOException reading media files: " + ioe.getMessage() );
            System.exit( 1 );
        }
    }

    private static Slide makeSlide( String textFile, String imageFile, String audioFile )
                                    throws FileNotFoundException, IOException,
                                           ContentException, UnsupportedEncodingException {

        // get a Slide instance from the factory
        Slide slide = Factory.getInstance().newSlide();

        // check and set those media files which were specified on the command line
        if( textFile != null ) {
            slide.setText( new Text( new File( textFile ), null ) );
        }
        if( imageFile != null ) {
            slide.setImage( new Image( new File( imageFile ) ) );
        }
        if( audioFile != null ) {
            slide.setAudio( new Audio( new File( audioFile ) ) );
        }

        return slide;
    }

    private static SubmitRequest createSubmitRequest( Slide slide,
                                                      String userName,
                                                      String recipient,
                                                      String mmscUrl )
                                                      throws APIException,
                                                             ContentException,
                                                             MessagingException,
                                                             IOException {

        // create the SubmitRequest object and use its methods to set different elements
        // of the request
        SubmitRequest request = new SubmitRequest();
        request.addRecipient( Recipient.Type.TO,
                              AddressType.NUMBER,
                              recipient );
        request.setVaspID( "my id" );
        if( allNumbers( userName ) ) {
            request.setSenderAddress( userName, AddressType.NUMBER );
        } else {
            request.setSenderAddress( userName + "@" + new URL( mmscUrl ).getHost(),
                                      AddressType.EMAIL );
        }
        request.setSubject( "Attachments you wanted..." );

        // set the MM content
        List slides = new ArrayList();
        slides.add( slide );
        request.setContent( slides );

        return request;
    }

    private static void printResponse( Response response ) {

        if( response instanceof SubmitResponse ) {

            SubmitResponse submitResponse = ( SubmitResponse ) response;

            // message id is the only important info in a submit response
            System.out.println( "Message ID for the submitted message: " +
                                submitResponse.getMessageID() );

        } else {

            // sendRequest returns either SubmitResponse or FaultResponse.
            FaultResponse resp = ( FaultResponse ) response;
            System.out.println( "Message Submission failed: " +
                                resp.getStatusText() + "(" + resp.getStatusCode() + ")" );

        }
    }

    private static void usage() {

        System.out.println( "Usage: java SimpleSender -text <text file>\n" +
                            "                         -image <image file>" +
                            "                         -audio <audio file>\n" +
                            "                         -to <MSISDN>\n"
                          );
        System.exit( 1 );
    }

    public static void main( String[] args ) {

        String userName = null;
        String password = null;
        String mmscUrl = null;
        String recipient = null;
        String imageFile = null;
        String textFile = null;
        String audioFile = null;

        // collect all the command line arguments we accept
        for( int i = 0; i < args.length; i++ ) {
            if( args[i].equals( "-to" ) ) {
                recipient = args[++i];
            } else if( args[i].equals( "-text" ) ) {
                textFile = args[++i];
            } else if( args[i].equals( "-audio" ) ) {
                audioFile = args[++i];
            } else if( args[i].equals( "-image" ) ) {
                imageFile = args[++i];
            } else {
                System.out.println( "illegal argument: " + args[i] );
                usage();
            }
        }

        // validation of command line args
        if( ( textFile == null || textFile.length() == 0 ) &&
            ( imageFile == null || imageFile.length() == 0 ) &&
            ( audioFile == null || audioFile.length() == 0 ) ) {
            System.out.println( "Atleast one media file must be specified" );
            usage();
        }
        if( recipient == null || recipient.length() == 0 ) {
            System.out.println( "Recipient(-to option) must be specified" );
            usage();
        }

        try {
            // read props file for mmscurl, username and password
            Properties props = new Properties();
            props.load( SimpleSender.class.getResourceAsStream( "SimpleSender.properties" ) );
            mmscUrl = props.getProperty( "mmscurl" );
            userName = props.getProperty( "username" );
            password = props.getProperty( "password" );
        } catch( IOException ioe ) {
            System.out.println( "IOException reading properties file: " + ioe.getMessage() );
            System.exit( 1 );
        }

        if( mmscUrl == null || mmscUrl.length() == 0 ) {
            System.out.println( "URL must be specified in the properties file SimpleSender.properties" );
            System.exit( 1 );
        }

        if( userName == null || userName.length() == 0 ) {
            System.out.println( "User name must be specified in the properties file SimpleSender.properties" );
            System.exit( 1 );
        }

        if( password == null || password.length() == 0 ) {
            System.out.println( "Password must be specified in the properties file SimpleSender.properties" );
            System.exit( 1 );
        }

        sendMM( mmscUrl, userName, password, recipient,
                textFile, imageFile, audioFile );
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
