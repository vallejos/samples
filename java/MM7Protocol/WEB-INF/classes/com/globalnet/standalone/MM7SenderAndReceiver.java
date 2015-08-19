package com.globalnet.standalone;

/*
 * MM7SenderAndReceiver.java
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

public class MM7SenderAndReceiver extends MessageListenerAdapter {

    public static void usage() {
        System.out.println("Usage: java MM7SenderAndReceiver -i <port number to listen on>\n" +
                           "                                 [-s <for SSL on receiver>]\n" +
                           "                                 [-u <user name> -p <password>]\n" +
                           "                                 -url <mmsc url>\n" +
                           "                                 {-slide text=<text_file>:image=<image_file>" +
                           ":audio=<audio_file>:duration=<float>\n" +
                           "                                 {-f <media file>}\n" +
                           "                                 {-ton <MSISDN>}\n" +
                           "                                 {-ccn <MSISDN>}\n" +
                           "                                 {-bccn <MSISDN>}\n" +
                           "                                 {-toe <Email>}\n" +
                           "                                 {-cce <Email>}\n" +
                           "                                 {-bcce <Email>}\n" +
                           "                                 {-tos <ShortCode>}\n" +
                           "                                 {-ccs <ShortCode>}\n" +
                           "                                 {-bccs <ShortCode>}\n" +
                           "                                 {-r <num times to repeat>}\n" +
                           "                                 {-t <num threads to use>}\n" +
                           "The first two command line options are for the Receiver and the rest " +
                           "are for the Sender.\n" +
                           "the -f and -slide options cannot be used together."
                          );
        System.exit( 1 );
    }

    public static void main( String[] args ) {
        int port = 0;
        boolean secure = false;
        String userName = null;
        String password = null;
        String mmscUrl = null;
        List slides = new ArrayList();
        Vector recipients = new Vector();
        Vector mmFiles = new Vector();
        int numThreads = 1;
        int numRepeat = 1;


        // collect all the command line options
        try {
            for ( int i = 0; i < args.length; i++ ) {
                if( args[i].equals( "-i" ) ) {
                    try {
                        port = Integer.parseInt( args[++i] );
                    } catch( NumberFormatException nfe ) {
                        System.out.println( "port must be a number" );
                    }
                } else if( args[i].equals( "-s" ) ) {
                    secure = true;
                } else if( args[i].equals( "-u" ) ) {
                    userName = args[++i];
                } else if( args[i].equals( "-p" ) ) {
                    password = args[++i];
                } else if( args[i].equals( "-url" ) ) {
                    mmscUrl = args[++i];
                } else if( args[i].equals( "-slide" ) ) {
                    slides.add( makeSlide( args[++i] ) );
                } else if( args[i].equals( "-f" ) ) {
                    String mmFile = args[++i];
                    mmFiles.add( mmFile );
                } else if( args[i].equals( "-ton" ) ) {
                    String recipient = args[++i];
                    recipients.add( new Recipient( Recipient.Type.TO,
                                                   AddressType.NUMBER,
                                                   recipient )
                                  );
                } else if( args[i].equals( "-ccn" ) ) {
                    String recipient = args[++i];
                    recipients.add( new Recipient( Recipient.Type.CC,
                                                   AddressType.NUMBER,
                                                   recipient )
                                  );
                } else if( args[i].equals( "-bccn" ) ) {
                    String recipient = args[++i];
                    recipients.add( new Recipient( Recipient.Type.BCC,
                                                   AddressType.NUMBER,
                                                   recipient )
                                  );
                } else if( args[i].equals( "-toe" ) ) {
                    String recipient = args[++i];
                    recipients.add( new Recipient( Recipient.Type.TO,
                                                   AddressType.EMAIL,
                                                   recipient )
                                  );
                } else if( args[i].equals( "-cce" ) ) {
                    String recipient = args[++i];
                    recipients.add( new Recipient( Recipient.Type.CC,
                                                   AddressType.EMAIL,
                                                   recipient )
                                  );
                } else if( args[i].equals( "-bcce" ) ) {
                    String recipient = args[++i];
                    recipients.add( new Recipient( Recipient.Type.BCC,
                                                   AddressType.EMAIL,
                                                   recipient )
                                  );
                } else if( args[i].equals( "-tos" ) ) {
                    String recipient = args[++i];
                    recipients.add( new Recipient( Recipient.Type.TO,
                                                   AddressType.SHORTCODE,
                                                   recipient )
                                  );
                } else if( args[i].equals( "-ccs" ) ) {
                    String recipient = args[++i];
                    recipients.add( new Recipient( Recipient.Type.CC,
                                                   AddressType.SHORTCODE,
                                                   recipient )
                                  );
                } else if( args[i].equals( "-bccs" ) ) {
                    String recipient = args[++i];
                    recipients.add( new Recipient( Recipient.Type.BCC,
                                                   AddressType.SHORTCODE,
                                                   recipient )
                                  );
                } else if( args[i].equals( "-t" ) ) {
                    try {
                        numThreads = Integer.parseInt( args[++i] );
                    } catch( NumberFormatException nfe ) {
                        System.out.println( "The -t argument must be folled by a number" );
                        usage();
                    }
                } else if( args[i].equals( "-r" ) ) {
                    try {
                        numRepeat = Integer.parseInt( args[++i] );
                    } catch( NumberFormatException nfe ) {
                        System.out.println( "The -r Argument must be folled by a number" );
                        usage();
                    }
                } else {
                    System.out.println( "illegal argument: " + args[i] );
                    usage();
                }
            }
        } catch( APIException e ) {
            System.out.println( "exception adding recipients: " + e.getMessage() );
            System.exit( 1 );
        } catch( ContentException ce ) {
            System.out.println( "exception making slide: " + ce.getMessage() );
            System.exit( 1 );
        } catch( FileNotFoundException fnfe ) {
            System.out.println( "exception making slide: " + fnfe.getMessage() );
            System.exit( 1 );
        } catch( IOException ioe ) {
            System.out.println( "exception making slide: " + ioe.getMessage() );
            System.exit( 1 );
        }
        // validate command line options
        if( port == 0 ) {
            System.out.println( "Port must be specified" );
            usage();
        }
        if( mmscUrl == null || mmscUrl.length() == 0 ) {
            System.out.println( "URL must be specified" );
            usage();
        }
        if( mmFiles.size() > 0 && slides.size() > 0 ) {
            System.out.println( "the -f and -slide options cannot be used together" );
            usage();
        }
        if( slides.size() == 0 && mmFiles.size() == 0 ) {
            System.out.println( "Atleast one slide or media file must be specified" );
            usage();
        }
        if( recipients.size() == 0 ) {
            System.out.println( "Atleast one Recipient must be specified" );
            usage();
        }

        MM7SenderAndReceiver obj = new MM7SenderAndReceiver();
        try {
            // create a connection to the relay and set our message listener
            RelayConnection conn = RelayConnection.createSenderAndReceiver( mmscUrl,
                                                                            userName,
                                                                            password,
                                                                            port,
                                                                            secure );
            sendMM( conn, slides, mmFiles, recipients ); 
            conn.setMessageListener( obj );
        } catch( APIException e ) {
            System.out.println( "APIException submitting message: " +
                                e.getLocalizedMessage() + "(" +
                                e.getErrorCode() + ")" );
        } catch( ContentException ce ) {
            System.out.println( "ContentException submitting message: " +
                                ce.getLocalizedMessage() );
        } catch( MessagingException me ) {
            System.out.println( "MessagingException submitting message: " +
                                me.getLocalizedMessage() );
        } catch( java.net.MalformedURLException mue ) {
            System.out.println( "bad url: " + mue.getMessage() );
        } catch( IOException ioe ) {
            System.out.println( "IOException: " + ioe.getMessage() );
        }
    }

    // the following two methods are part of the MessageListener interface which we implement
    // here. We extend the MessageListenerAdapter utility class so that we can just implement
    // the methods we are interested in.
    public Response processDeliverRequest( DeliverRequest deliverRequest ) {
        // do whatever we want with the DeliverRequest object here
        try {
            System.out.println( "TransactionID: " + deliverRequest.getTransactionID() );
            System.out.println( "Subject: " + deliverRequest.getSubject() );
            Object content = deliverRequest.getContent();
            if( content instanceof MimeBodyPart ) {
                ( ( MimeBodyPart ) content ).writeTo( System.out );
            } else if( content instanceof MultimediaContent ) {
                ( ( MultimediaContent ) content ).writeTo( System.out );
            }
        } catch( java.io.IOException ie ) {
            System.out.println( "IOException: " + ie.getMessage() );
        } catch( APIException ae ) {
            System.out.println( "APIException submitting message: " +
                                ae.getLocalizedMessage() + "(" +
                                ae.getErrorCode() + ")" );
        } catch( ContentException ce ) {
            System.out.println( "ContentException submitting message: " +
                                ce.getLocalizedMessage() );
        } catch( MessagingException me ) {
            System.out.println( "MessagingException submitting message: " +
                                me.getMessage() );
        }

        // create a response object and send it back to the Openwave MMSC relay
        DeliverResponse response = new DeliverResponse();
        response.setStatusCode( ErrorCode.SUCCESS );
        response.setStatusText( "got it!" );

        return response;
    }

    public Response processDeliveryReport( DeliveryReport deliveryReport ) {
        // do whatever we want with the DeliveryReport object here
        System.out.println( "MessageID: " + deliveryReport.getMessageID() );
        System.out.println( "MMStatus: " + deliveryReport.getMMStatus() );

        // create a response object and send it back to the relay
        DeliveryReportResponse response = new DeliveryReportResponse();
        response.setStatusCode( ErrorCode.SUCCESS );
        response.setStatusText( "got it!" );

        return response;
    }

    private static void sendMM( RelayConnection conn,
                                List slides,
                                Vector mmFiles,
                                Vector recipients )
                                throws APIException,
                                       ContentException, 
                                       MessagingException, 
                                       IOException {
        // now create a SubmitRequest object, use the connection object created earlier
        // to send it to the relay
        SubmitRequest request = createSubmitRequest( slides, mmFiles, recipients );
        Response response = null;
        try {
            response = conn.sendRequest( request );
            SubmitResponse submitResponse = ( SubmitResponse ) response;
            printResponse( submitResponse );
        } catch( ClassCastException cce ) {
            // if the object could not be cast to SubmitResponse, it must be a FaultResponse.
            FaultResponse resp = ( FaultResponse ) response;
            System.out.println( "Message Submission failed: " +
                                resp.getFaultString() +
                                "(" + resp.getFaultCode() + ")" );
        }
    }

    private static SubmitRequest createSubmitRequest( List slides,
                                                      Vector mmFiles,
                                                      Vector recipients )
                                                      throws ContentException,
                                                             MessagingException, 
                                                             IOException {
        // create the SubmitRequest object and use its methods to set different elements
        // of the request
        SubmitRequest request = new SubmitRequest();
        request.setVaspID( "my id" );
        request.addRecipients( recipients );
        request.setMessageClass( SubmitRequest.MessageClass.PERSONAL );
        request.setPriority( SubmitRequest.Priority.LOW );
        request.setDeliveryReport( true );

        try {
            request.setSubject( "Attachments you wanted..." );
        } catch( APIException e ) {
            System.out.println( "exception adding subject: " + e.getMessage() );
            System.exit( 1 );
        }

        // set the MM content
        setContent( request, slides, mmFiles );

        return request;
    }

    private static void setContent( SubmitRequest request,
                                    List slides,
                                    Vector mmFiles ) throws ContentException,
                                                            MessagingException,
                                                            IOException {
        if( slides.size() > 0 ) {
            request.setContent( slides );
        } else if( mmFiles.size() > 1 ) {
            // create MimeBodyParts for the content files specified on the command line
            MimeMultipart multipart = new MimeMultipart( "related" );
            for( int i = 0; i < mmFiles.size(); i++ ) {
                String fileName = ( String ) mmFiles.get( i );
                try {
                    MimeBodyPart part = new MimeBodyPart();
                    DataSource source = new FileDataSource( fileName );
                    part.setDataHandler( new DataHandler( source ) );
                    File file = new File( fileName );
                    part.setFileName( file.getName() );
                    part.setHeader( "Content-ID", "<" + file.getName() + ">" );
                    multipart.addBodyPart( part );
                } catch( MessagingException me ) {
                    System.out.println( "MessagingException: " + me.getMessage() );
                    System.exit( 1 );
                }
            }
            request.setContent( multipart );
        } else if( mmFiles.size() == 1 ) {
            try {
                String fileName = ( String ) mmFiles.get( 0 );
                MimeBodyPart part = new MimeBodyPart();
                DataSource source = new FileDataSource( fileName );
                part.setDataHandler( new DataHandler( source ) );
                File file = new File( fileName );
                part.setFileName( file.getName() );
                part.setHeader( "Content-ID", "<" + file.getName() + ">" );
                request.setContent( part );
            } catch( MessagingException me ) {
                System.out.println( "MessagingException: " + me.getMessage() );
                System.exit( 1 );
            }
        }
    }

    private static void printResponse( SubmitResponse response ) {
        // message id is probably the only important info in a response
        System.out.println( "Message ID for the submitted message: " + response.getMessageID() );
    }

    private static Slide makeSlide( String slideParam )
                                    throws FileNotFoundException,
                                           IOException,
                                           ContentException,
                                           UnsupportedEncodingException {
        String textFile = null;
        String imageFile = null;
        String audioFile = null;
        int duration = 0;

        StringTokenizer parser = new StringTokenizer( slideParam, "=:" );
        while( parser.hasMoreTokens() ) {
            String token = parser.nextToken();
            if( token.equals( "text" ) ) {
                textFile = parser.nextToken();
            } else if( token.equals( "image" ) ) {
                imageFile = parser.nextToken();
            } else if( token.equals( "audio" ) ) {
                audioFile = parser.nextToken();
            } else if( token.equals( "duration" ) ) {
                try {
                    duration = Integer.parseInt( parser.nextToken() );
                } catch( NumberFormatException nfe ) {
                    System.out.println( "duration value must be a floating point number" );
                    usage();
                }
            } else {
                System.out.println( "Illegal parameter name in value for -slide option" );
                usage();
            }
        }

        if( textFile == null && imageFile == null && audioFile == null ) {
            System.out.println( "atleast one media file must be specified" );
            usage();
        }

        Slide slide = Factory.getInstance().newSlide();

        if( textFile != null ) {
            slide.setText( new Text( new File( textFile ), null ) );
        }
        if( imageFile != null ) {
            slide.setImage( new Image( new File( imageFile ) ) );
        }
        if( audioFile != null ) {
            slide.setAudio( new Audio( new File( audioFile ) ) );
        }
        if( duration > 0 ) {
            slide.setDuration( duration );
        }

        return slide;
    }
}
