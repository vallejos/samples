package com.globalnet.standalone;

/*
 * GetDeviceProfile.java
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

public class GetDeviceProfile {

    private static void sendReq( String mmscUrl, 
                                 String userName,
                                 String password,
                                 String targetUser) {

        try {
            // create a connection to the mmsc
            RelayConnection conn = RelayConnection.createSender( mmscUrl, userName, password );

            // now create a DeviceProfileRequest object, use the connection object created earlier
            // to send it to the relay
            DeviceProfileRequest request = createDeviceProfileRequest(targetUser, userName);

            // now send the request using the connection created above
            Response response = conn.sendRequest( request );
            printResponse( response, targetUser );
        } catch( APIException ae ) {
            System.out.println( "APIException: " + ae.getMessage() );
            System.exit( 1 );
        } catch( ContentException ce ) {
            System.out.println( "ContentException: " + ce.getMessage() );
            System.exit( 1 );
        } catch( MalformedURLException mue ) {
            System.out.println( "bad url " + mue.getMessage() );
            System.exit( 1 );
        } catch( IOException ioe ) {
            System.out.println( "IOException reading media files: " + ioe.getMessage() );
            System.exit( 1 );
        }
    }

    private static DeviceProfileRequest createDeviceProfileRequest( String user, String vaspId )
                                                      throws APIException {

        // create the DeviceProfileRequest object and use its methods 
        // to set different elements of the request
        DeviceProfileRequest request = new DeviceProfileRequest();
        request.addNumber( user );
        request.setVaspID( vaspId );

        return request;
    }

    private static void printResponse( Response response, String targetUser ) {

        if( response instanceof DeviceProfileResponse ) {

            DeviceProfileResponse deviceProfileResponse = ( DeviceProfileResponse ) response;

            // message id is the only important info in a submit response
            System.out.println( "Information for user " + targetUser );

            UserDevice ud = deviceProfileResponse.getUserDevice( targetUser );
            if ( ud.getStatusCode() == ErrorCode.SUCCESS ) {
                if ( ud.getType() == UserDevice.PROFILEURL ) {
                    System.out.println( "\tProfile URL: " + ud.getDescription() );
                } else {
                    System.out.println( "\tUser Agent: " + ud.getDescription() );
                }
            } else {
                System.out.println( "\tError: " + ud.getStatusCode() );
            }

        } else {

            // sendRequest returns either SubmitResponse or FaultResponse.
            FaultResponse resp = ( FaultResponse ) response;
            System.out.println( "Message Submission failed: " +
                                resp.getStatusText() + "(" + resp.getStatusCode() + ")" );

        }
    }

    private static void usage() {

        System.out.println( "Usage: java GetDeviceProfile <phoneNumber>\n"
                          );
        System.exit( 1 );
    }

    public static void main( String[] args ) {

        String userName = null;
        String password = null;
        String mmscUrl = null;
        String phoneNumber = null;

        // collect the phone number from command line
        if ( args.length == 0 ) {
            usage();
        }
        phoneNumber = args[0];

        // validation of command line arg
        if ( phoneNumber == null || phoneNumber.length() == 0 ) {
            System.out.println( "Phone number must be specified" );
            usage();
        }

        try {
            // read props file for mmscurl, username and password
            Properties props = new Properties();
            props.load( GetDeviceProfile.class.getResourceAsStream( "GetDeviceProfile.properties" ) );
            mmscUrl = props.getProperty( "mmscurl" );
            userName = props.getProperty( "username" );
            password = props.getProperty( "password" );
        } catch( IOException ioe ) {
            System.out.println( "IOException reading properties file: " + ioe.getMessage() );
            System.exit( 1 );
        }

        if( mmscUrl == null || mmscUrl.length() == 0 ) {
            System.out.println( "URL must be specified in the properties file GetDeviceProfile.properties" );
            System.exit( 1 );
        }

        if( userName == null || userName.length() == 0 ) {
            System.out.println( "User name must be specified in the properties file GetDeviceProfile.properties" );
            System.exit( 1 );
        }

        if( password == null || password.length() == 0 ) {
            System.out.println( "Password must be specified in the properties file GetDeviceProfile.properties" );
            System.exit( 1 );
        }

        sendReq( mmscUrl, userName, password, phoneNumber );
    }

}
