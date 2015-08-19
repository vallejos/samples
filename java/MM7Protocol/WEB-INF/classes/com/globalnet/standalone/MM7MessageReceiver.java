package com.globalnet.standalone;

/*
 * MM7MessageReceiver.java
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
import java.io.File;
import java.net.PasswordAuthentication;
import java.util.Iterator;
import java.util.Set;
import java.util.Vector;
import java.util.Calendar;
import java.util.Date;
import java.text.DateFormat;
import javax.mail.internet.*;
import javax.mail.MessagingException;

public class MM7MessageReceiver extends MessageListenerAdapter {

    public static void usage() {
        System.out.println("Usage: java MM7MessageReceiver -i <port number>\n" +
                           "                               {-u <username> -p <password>} [-s]\n" +
                           "                               [-dstDir <directory>]\n" +
                           "where directory is used to store the incoming MM's parts.\n" +
                           "Use -s option to enable SSL");
        System.exit( 1 );
    }

    public static void main( String[] args ) {
        int port = 0;
        boolean secure = false;
        String user = null;
        String passwd = null;
        File dstDir = null;
        Vector authenticators = new Vector();

        // collect all the command line options
        for ( int i = 0; i < args.length; i++ ) {
            if( args[i].equals( "-i" ) ) {
                try {
                    port = Integer.parseInt( args[++i] );
                } catch( NumberFormatException nfe ) {
                    System.out.println( "port must be a number" );
                }
            } else if( args[i].equals( "-s" ) ) {
                secure = true;
                i++;
            } else if( args[i].equals( "-u" ) ) {
                user = args[++i];
            } else if( args[i].equals( "-p" ) ) {
                passwd = args[++i];
                if( user != null && user.length() != 0 &&
                    passwd != null && passwd.length() != 0 ) {
                    authenticators.add( new PasswordAuthentication( user, passwd.toCharArray() ) );
                }
            } else if( args[i].equals( "-dstDir" ) ) {
                dstDir = new File( args[++i] );
                if( dstDir.isDirectory() == false ) {
                    System.out.println( "-dstDir argument must be a directory" );
                    usage();
                }
                if( dstDir.exists() == false ) {
                    dstDir.mkdir();
                }
            } else {
                System.out.println( "illegal argument: " + args[i] );
                usage();
            }
        }
        // validate command line options
        if( port == 0 ) usage();

        MM7MessageReceiver receiver = new MM7MessageReceiver();
        receiver.mDstDir = dstDir;
        try {
            // create a connection to the Openwave MMSC relay and set our message listener
            RelayConnection conn = RelayConnection.createReceiver( port, secure );
            // set authentication database for authenticating incoming connections
            if( ! authenticators.isEmpty() ) {
                conn.setAuthenticators( authenticators );
                conn.setAuthType( RelayConnection.AuthenticationType.DIGEST );
            }

            conn.setMessageListener( receiver );
        } catch( APIException e ) {
            System.out.println( "APIException submitting message: " +
                                e.getLocalizedMessage() + "(" +
                                e.getErrorCode() + ")" );
        }
    }

    // the following two methods are part of the MessageListener interface which we implement
    // here. We extend the MessageListenerAdapter utility class so that we can just implement
    // the methods we are interested in.
    public Response processDeliverRequest( DeliverRequest deliverRequest ) {

        System.out.println( "TransactionID: " + deliverRequest.getTransactionID() );
        System.out.println( "Linked ID: " + deliverRequest.getLinkedID() );
        System.out.println( "Sender: " + deliverRequest.getSender() );
        try {
            Vector recipients = deliverRequest.getRecipients();
            if( recipients != null ) {
                for( int i = 0; i < recipients.size(); i++ ) {
                    Recipient recipient = ( Recipient ) recipients.get( i );
                    System.out.println( recipient.getType() + ": " + recipient.getAddress() );
                }
            }

            System.out.println( "Subject: " + deliverRequest.getSubject() );
            Calendar date = deliverRequest.getDate();
            if( date != null ) {
                System.out.println( "Date: " + DateFormat.getDateTimeInstance()
                                                         .format( date.getTime() ) );
            }
            Object content = deliverRequest.getContent();
            if( content instanceof MultimediaContent ) {
                MultimediaContent mmContent = ( MultimediaContent ) content;
                mmContent.writeTo( System.out );
                printLockStatus( mmContent );
                if( mmContent.isSmilBased() && mDstDir != null ) {
                    for( int i = 0; i < mmContent.getNumSlides(); i++ ) {
                        Slide slide = mmContent.getSlide( i );
                        Set mediaObjects = slide.getMediaObjects();
                        if( mediaObjects != null ) {
                            Iterator iter = mediaObjects.iterator();
                            while( iter.hasNext() ) {
                                MediaObject mediaObject = ( MediaObject ) iter.next();
                                mediaObject.save( mDstDir );
                            }
                        }
                    }
                }
            } else if( content instanceof MimeBodyPart ) {
                ( ( MimeBodyPart ) content ).writeTo( System.out );
            }
        } catch( APIException e ) {
            e.printStackTrace();
            System.out.println( "APIException processing request: " +
                                e.getLocalizedMessage() + "(" +
                                e.getErrorCode() + ")" );
        } catch( java.io.IOException ie ) {
            System.out.println( "IOException: " + ie.getMessage() );
        } catch( ContentException ce ) {
            System.out.println( "ContentException: " + ce.getMessage() );
        } catch( MessagingException me ) {
            System.out.println( "MessagingException: " + me.getMessage() );
        }

        // create a response object and send it back to the relay
        DeliverResponse response = new DeliverResponse();
        response.setStatusCode( ErrorCode.SUCCESS );
        response.setServiceCode( "sample-service-code" );
        response.setStatusText( "got it!" );

        return response;
    }

    public Response processDeliveryReport( DeliveryReport deliveryReport ) {

        System.out.println( "MessageID: " + deliveryReport.getMessageID() );
        System.out.println( "Original Sender: " + deliveryReport.getSender() );
        System.out.println( "Original Recipient: " + deliveryReport.getRecipient() );
        System.out.println( "MMStatus: " + deliveryReport.getMMStatus() );

        // create a response object and send it back to the Openwave MMSC relay
        DeliveryReportResponse response = new DeliveryReportResponse();
        response.setStatusCode( ErrorCode.SUCCESS );
        response.setStatusText( "got it!" );

        return response;
    }

    public Response processReadReply( ReadReply readReply ) {

        System.out.println( "MessageID: " + readReply.getMessageID() );
        System.out.println( "Original Sender: " + readReply.getSender() );
        System.out.println( "Original Recipient: " + readReply.getRecipient() );
        System.out.println( "MMStatus: " + readReply.getMMStatus() );

        // create a response object and send it back to the Openwave MMSC relay
        ReadReplyResponse response = new ReadReplyResponse();
        response.setStatusCode( ErrorCode.SUCCESS );
        response.setStatusText( "got it!" );

        return response;
    }

    private void printLockStatus( MultimediaContent mmContent ) {
        if( mmContent.isSmilBased() ) {
            for( int i = 0; i < mmContent.getNumSlides(); i++ ) {
                Slide slide = mmContent.getSlide( i );
                System.out.println("Slide " + i + ":");
                Text text = slide.getText();
                if( text != null ) {
                    System.out.println("Text Locked? " + text.getForwardLock());
                }
                Image image = slide.getImage();
                if( image != null ) {
                    System.out.println("Image Locked? " + image.getForwardLock());
                    if( image.getForwardLock() ) {
                        System.out.println("Rights : " + image.getRights());
                    }
                }
                Audio audio = slide.getAudio();
                if( audio != null ) {
                    System.out.println("Audio Locked? " + audio.getForwardLock());
                }
            }
        }
    }

    File mDstDir;

}
