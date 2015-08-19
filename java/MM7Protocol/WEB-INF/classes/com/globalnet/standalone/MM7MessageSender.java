package com.globalnet.standalone;

/*
 * MM7MessageSender.java
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
import com.openwave.mms.mm7.soap.SOAPConsts;
import com.openwave.mms.content.*;
import javax.mail.internet.*;
import javax.mail.*;
import javax.activation.*;
import java.util.*;
import java.io.*;
import java.net.*;

public class MM7MessageSender {

    public static void usage() {

        System.out.println( "Usage: java MM7MessageSender [-u <user name> -p <password>]\n" +
                            "                             -url <mmsc url>\n" +
                            "                             {-slide text=<text_file>:image=<image_file>:video=<video_file>" +
                            ":audio=<audio_file>:duration=<int ms>:lock=[t|i|a]}\n" +
                            "                             {-f <media file>}\n" +
                            "                             {-ton <MSISDN>}\n" +
                            "                             {-ccn <MSISDN>}\n" +
                            "                             {-bccn <MSISDN>}\n" +
                            "                             {-toe <Email>}\n" +
                            "                             {-cce <Email>}\n" +
                            "                             {-bcce <Email>}\n" +
                            "                             {-tos <ShortCode>}\n" +
                            "                             {-ccs <ShortCode>}\n" +
                            "                             {-bccs <ShortCode>}\n" +
                            "                             [-t <num threads to use>]\n" +
                            "                             [-i <num iterations per thread>]\n" +
                            "                             [-q <quit on exception>]\n" +
                            "                             [-v <MM7 Namespace Version>]\n" +
                            "                             [-d - specify for writing debug output to stdout]\n" +
                            "the -f and -slide options cannot be used together.\n" +
                            "the lock parameter in the slide option can have \n" +
                            "any combination of the letters t, i, a.\n" +
                            "-v can have REL-5-MM7-1-0, REL-5-MM7-1-1, REL-5-MM7-1-2 or REL-5-MM7-1-3 as values."
                          );
        System.exit( 1 );
    }

    public static void main( String[] args ) {

        String userName = null;
        String password = null;
        String mmscUrl = null;
        Vector recipients = new Vector();
        int numThreads = 1;
        int numIter = 1;
        boolean debug = false;
        boolean quitOnException = false;
        List slides = new ArrayList();
        Vector mmFiles = new Vector();
        Namespace mm7Namespace;

        try {
            // collect all the command line arguments we accept
            for( int i = 0; i < args.length; i++ ) {
                if( args[i].equals( "-u" ) ) {
                    userName = args[++i];
                } else if( args[i].equals( "-p" ) ) {
                    password = args[++i];
                } else if( args[i].equals( "-url" ) ) {
                    mmscUrl = args[++i];
                } else if( args[i].equals( "-d" ) ) {
                    i++;
                    debug = true;
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
                        System.out.println( "The -t argument must be followed by a number" );
                        usage();
                    }
                } else if( args[i].equals( "-i" ) ) {
                    try {
                        numIter = Integer.parseInt( args[++i] );
                    } catch( NumberFormatException nfe ) {
                        System.out.println( "The -i argument must be followed by a number" );
                        usage();
                    }
                } else if( args[i].equals( "-v" ) ) {
                    String namespaceStr = args[++i];
                    for( int j = 0; j < SOAPConsts.MM7Namespaces.length; j++ ) {
                        if( SOAPConsts.MM7Namespaces[j].endsWith( namespaceStr ) ) {
                            namespaceStr = SOAPConsts.MM7Namespaces[j];
                        }
                    }
                    mm7Namespace = Namespace.valueOf( namespaceStr );
                    if( mm7Namespace == null ) {
                        System.out.println( "Unsupported namespace: " + args[i] );
                        usage();
                    }
                    Request.setNamespace( mm7Namespace );
                } else if( args[i].equals( "-q" ) ) {
                    i++;
                    quitOnException = true;
                } else if( args[i].equals( "-slide" ) ) {
                    slides.add( makeSlide( args[++i] ) );
                } else {
                    System.out.println( "illegal argument: " + args[i] );
                    usage();
                }
            }
        } catch( APIException e ) {
            System.out.println( "exception adding recipient: " + e.getMessage() );
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

        // validation of command line args
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

        long timeBefore = System.currentTimeMillis();
        TaskThread workers[] = new TaskThread[ numThreads ];
        for( int i = 0; i < numThreads; i++ ) {
            workers[i] = new TaskThread( mmscUrl,
                                         userName,
                                         password,
                                         slides,
                                         mmFiles,
                                         recipients,
                                         numIter,
                                         quitOnException,
                                         debug );
            workers[i].start();
        }
        int totalItersSucceeded = 0;
        for(int i = 0; i < numThreads; i++) {
            try {
                workers[i].join();
                totalItersSucceeded += workers[i].getNumItersSucceeded();
            } catch(InterruptedException e) {
                System.out.println( Thread.currentThread().getName() + " Interrupted!" );
            }
        }
        long timeAfter = System.currentTimeMillis();
        float timeSecs = ( float ) ( timeAfter - timeBefore ) / 1000;
        System.out.println( "Finished " + numThreads + " threads each with " +
                            numIter + " iterations" );
        System.out.println( totalItersSucceeded + " iterations succeeded" );
        System.out.println( "Messages per sec: " + ( numThreads * numIter ) / timeSecs );
    }

    private static Slide makeSlide( String slideParam )
                                    throws FileNotFoundException,
                                           IOException,
                                           ContentException,
                                           UnsupportedEncodingException {
        String textFile = null;
        String imageFile = null;
        String audioFile = null;
        String videoFile = null;
        boolean lockText = false;
        boolean lockImage = false;
        boolean lockAudio = false;
        int duration = 0;

        StringTokenizer parser = new StringTokenizer( slideParam, "=:" );
        while( parser.hasMoreTokens() ) {
            String token = parser.nextToken();
            if( token.equals( "text" ) ) {
                textFile = parser.nextToken();
            } else if( token.equals( "image" ) ) {
                imageFile = parser.nextToken();
            } else if( token.equals( "video" ) ) {
                videoFile = parser.nextToken();
            } else if( token.equals( "audio" ) ) {
                audioFile = parser.nextToken();
            } else if( token.equals( "duration" ) ) {
                try {
                    duration = Integer.parseInt( parser.nextToken() );
                } catch( NumberFormatException nfe ) {
                    System.out.println( "duration value must be a floating point number" );
                    usage();
                }
            } else if( token.equals( "lock" ) ) {
                String locks = parser.nextToken();
                if( locks.indexOf( 't' ) != -1 ) {
                    lockText = true;
                } else if( locks.indexOf( 'i' ) != -1 ) {
                    lockImage = true;
                } else if( locks.indexOf( 'a' ) != -1 ) {
                    lockAudio = true;
                }
            } else {
                System.out.println( "Illegal parameter name in value for -slide option" );
                usage();
            }
        }

        if( textFile == null && imageFile == null &&
            audioFile == null && videoFile == null ) {
            System.out.println( "atleast one media file must be specified" );
            usage();
        }

        Slide slide = Factory.getInstance().newSlide();

        if( textFile != null ) {
            Text text = new Text( new File( textFile ) );
            if( lockText ) {
                text.setForwardLock();
            }
            slide.setText( text );
        }
        if( imageFile != null ) {
            Image image = new Image( new File( imageFile ) );
            if( lockImage ) {
                image.setForwardLock();
            }
            slide.setImage( image );
        }
        if( videoFile != null ) {
            Video video = new Video( new File( videoFile ) );
            slide.setVideo( video );
        }
        if( audioFile != null ) {
            Audio audio = new Audio( new File( audioFile ) );
            if( lockAudio ) {
                audio.setForwardLock();
            }
            slide.setAudio( audio );
        }
        if( duration > 0 ) {
            slide.setDuration( duration );
        }

        return slide;
    }
}


class TaskThread extends Thread {
    private String mmscUrl;
    private String userName;
    private String password;
    private List slides;
    private Vector mmFiles;
    private Vector recipients;
    private int numIters;
    private int numItersSucceeded;
    private boolean quitOnException;
    private boolean debug;

    public TaskThread( String mmscUrl,
                       String userName,
                       String password,
                       List   slides,
                       Vector mmFiles,
                       Vector recipients,
                       int numIters,
                       boolean quitOnException,
                       boolean debug ) {
        this.mmscUrl = mmscUrl;
        this.userName = userName;
        this.password = password;
        this.slides = slides;
        this.mmFiles = mmFiles;
        this.recipients = recipients;
        this.numIters = numIters;
        this.quitOnException = quitOnException;
        this.debug = debug;
        numItersSucceeded = numIters;
    }

    public void run() {

        for( int i = 0; i < numIters; i++ ) {
            System.out.println("[Start " + Thread.currentThread() + " Iteration " + i + "]" );
            try {
                // create a connection to an relay and send the MM
                RelayConnection conn = RelayConnection.createSender( mmscUrl, userName, password );
                conn.setWeakCN( true );
                sendMM( conn, userName, slides, mmFiles, recipients ); 
            } catch( APIException e ) {
                numItersSucceeded--;
                System.out.println( "APIException submitting message: " +
                                    e.getMessage() + "(" +
                                    e.getErrorCode() + ")" );
                if( quitOnException )
                    System.exit( 1 );
            } catch( ContentException ce ) {
                numItersSucceeded--;
                System.out.println( "ContentException submitting message: " +
                                    ce.getMessage() );
                if( quitOnException )
                    System.exit( 1 );
            } catch( MessagingException me ) {
                numItersSucceeded--;
                System.out.println( "MessagingException submitting message: " +
                                    me.getMessage() );
                if( quitOnException )
                    System.exit( 1 );
            } catch( java.net.MalformedURLException mue ) {
                numItersSucceeded--;
                System.out.println( "bad url: " + mue.getMessage() );
                if( quitOnException )
                    System.exit( 1 );
            } catch( IOException ioe ) {
                numItersSucceeded--;
                System.out.println( "IOException: " + ioe.getMessage() );
                if( quitOnException )
                    System.exit( 1 );
            }
            System.out.println("[End " + Thread.currentThread() + " Iteration " + i + "]" );
        }
    }

    public int getNumItersSucceeded() { return numItersSucceeded; }

    private void sendMM( RelayConnection conn,
                         String userName,
                         List slides,
                         Vector mmFiles,
                         Vector recipients ) throws APIException,
                                                    ContentException,
                                                    MessagingException,
                                                    IOException {

        // now create a SubmitRequest object, use the connection object created earlier
        // to send it to the relay
        SubmitRequest request = createSubmitRequest( slides,
                                                     mmFiles,
                                                     userName,
                                                     recipients
                                                   );
        Response response = null;
        try {
            response = conn.sendRequest( request );
            SubmitResponse submitResponse = ( SubmitResponse ) response;
            printResponse( submitResponse );
        } catch( ClassCastException cce ) {
            // if the object could not be cast to SubmitResponse, it must be a FaultResponse.
            FaultResponse resp = ( FaultResponse ) response;
            System.out.println( "Message Submission failed: " +
                                "faultstring=" + resp.getFaultString() + "," +
                                "faultcode=" + resp.getFaultCode() + "," +
                                "statustext=" + resp.getStatusText() + "," +
                                "statuscode=" + resp.getStatusCode() );
        }
    }

    private SubmitRequest createSubmitRequest( List slides,
                                               Vector mmFiles,
                                               String userName,
                                               Vector recipients )
                                               throws APIException,
                                                      ContentException,
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
        request.setReadReply( true );
        request.setSubject( "Attachments you wanted..." );

        Calendar twoDaysFromNow = Calendar.getInstance();
        twoDaysFromNow.add( Calendar.DATE, 2 );
        request.setExpiry( twoDaysFromNow );

        // distribution indicator
        request.setDistributionIndicator( true );

        // set the MM content
        setContent( request, slides, mmFiles );

        return request;
    }

    private void setContent( SubmitRequest request,
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

    private void printResponse( SubmitResponse response ) {

        // message id is probably the only important info in a response
        System.out.println( "Message ID for the submitted message: " + response.getMessageID() );
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
