package com.globalnet.mm7;

import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;
import java.util.HashMap;
import java.util.Properties;

import javax.activation.DataHandler;
import javax.activation.FileDataSource;
import javax.mail.MessagingException;
import javax.mail.internet.HeaderTokenizer;
import javax.mail.internet.MimeBodyPart;
import javax.mail.internet.MimeMultipart;
import javax.mail.internet.ParameterList;
import javax.mail.internet.ParseException;
import javax.xml.soap.SOAPException;
import org.apache.log4j.Logger;

import org.jdom.Document;

import com.globalnet.servlet.Base64;
import com.globalnet.standalone.SimpleSender;

public class MM7SubmitRequest {

    static Logger logger = Logger.getLogger(MM7SubmitRequest.class);

    public static String sendMessage(String mmscUrl, String userName, String password, String subject,
            String recipient, String strText, String pathFile, String file, String contentType,
            String strFileEncodingType, String strShortCode) throws MM7SubmitRequestException {
        String strContentType = null;
        String hostIP = null;
        String strNameSpace = null;
        String strVASPID = "9222";
        String strVASID = "9222";
        //String strShortCode = "2002";

        boolean addTransactionID = false;
        boolean addHeaderNamespace = false;
        boolean addBodyNamespace = false;
        boolean useAuthentication = false;

        try {
            System.out.println("Around here...");
            try {
                Properties props = new Properties();
                props.load(MM7SubmitRequest.class.getResourceAsStream("/resources/SimpleSender.properties"));
                hostIP = props.getProperty("host_ip");
                strNameSpace = props.getProperty("ns");

                strVASPID = props.getProperty("VASPID");
                strVASID = props.getProperty("VASID");
                if (strShortCode == null) {
                    strShortCode = props.getProperty("ShortCode");
                }

                addTransactionID = props.getProperty("addTransactionID") == null || props.getProperty("addTransactionID").equalsIgnoreCase("False") ? false : true;
                addHeaderNamespace = props.getProperty("addHeaderNamespace") == null || props.getProperty("addHeaderNamespace").equalsIgnoreCase("False") ? false : true;
                addBodyNamespace = props.getProperty("addBodyNamespace") == null || props.getProperty("addBodyNamespace").equalsIgnoreCase("False") ? false : true;
                useAuthentication = props.getProperty("authentication") == null || props.getProperty("authentication").equalsIgnoreCase("False") ? false : true;

            } catch (IOException e) {
                System.out.println("No se pudo obtener datos del Properties.");
            }


            URL url = new URL(mmscUrl);

            System.out.println("Wanna connect?");
            URLConnection connection = url.openConnection();
            System.out.println("w00t!");

            // Let the run-time system (RTS) know that we want input.
            connection.setDoInput(true);

            // Let the RTS know that we want to do output.
            connection.setDoOutput(true);
            // No caching, we want the real thing.
            // connection.setUseCaches(false);

            String strContentId = "<content_id>";

            if (hostIP != null) {
                connection.setRequestProperty("Host", hostIP);
            }

            connection.setRequestProperty("Connection", "keep-alive");
            connection.setRequestProperty("SOAPAction", "\"\"");

            if (useAuthentication) {
                String strBase64 = Base64.encodeString(userName + ":" + password, false);
                connection.setRequestProperty("Authorization", " Basic " + strBase64);
            }

            //strContentType = "\"multipart/related\"; type=\"text/xml\"; ";
            strContentType = "multipart/related; type=\"text/xml\"; ";
            strContentType += "start=\"" + strContentId + "\"; ";

            // Declaro el MimeMultiPart
            MimeMultipart multiPart = new MimeMultipart("related");

            MimeBodyPart soapPart = new MimeBodyPart();

            // Creo el documento SOAP
            Document doc = SOAPMessage.getMessage(strNameSpace, strVASPID, strVASID, strShortCode, recipient, subject, addTransactionID, addHeaderNamespace, addBodyNamespace);

            // Agrego el SOAP como Bodypart
            String soapText = SOAPMessage.getStringFromDocument(doc, strNameSpace);
            soapPart.setText(soapText);

            soapPart.addHeader("Content-Type", "text/xml");
            soapPart.setContentID(strContentId);

            // Agrego el SoapPart al MimeMultiPart
            multiPart.addBodyPart(soapPart);

            // Declaro el Multipart interno para los contenidos
            MimeMultipart mmpContenido = new MimeMultipart("related");

            //######################################################################
            //				Agrego contenidos smil al Multipart
            //######################################################################
            if (strText != null) {
                MimeBodyPart contenido = new MimeBodyPart();

                String strSMILtext;
                strSMILtext = "<smil>";
                strSMILtext += "	<head>";
                strSMILtext += "		<layout>";
                strSMILtext += "			<root-layout width=\"100%\" height=\"100%\"/>";
                strSMILtext += "			<region id=\"multimedia1\" width=\"100%\" height=\"100%\"/>";
                strSMILtext += "			<region id=\"Text\" width=\"100%\" height=\"100%\" top=\"100%\"/>";
                strSMILtext += "		</layout>";
                strSMILtext += "	</head>";
                strSMILtext += "	<body>";
                strSMILtext += "		<par>";
                strSMILtext += "			<img class=\"t\" src=\"cid:multimedia1\" region=\"multimedia1\"/>";
                strSMILtext += "			<text src=\"cid:text_part\" region=\"Text\"/>";
                strSMILtext += "		</par>";
                strSMILtext += "	</body>";
                strSMILtext += "</smil>";

                contenido.setText(strSMILtext);

                contenido.addHeader("content-type", "application/smil");
                contenido.setContentID("<smil>");

                // Agrego el Bodypart al Multipart
                mmpContenido.addBodyPart(contenido);
            }
            //######################################################################
            //######################################################################

            if (contentType.equalsIgnoreCase("image")) {
                contentType += "/gif";
            } else if (contentType.equalsIgnoreCase("video")) {
                contentType += "/3gpp";
            }

            //######################################################################
            // 				Agrego contenidos multimedia al Multipart
            //######################################################################
            MimeBodyPart contenido = new MimeBodyPart();

            FileDataSource ds = new FileDataSource(pathFile + file);
            contenido.setDataHandler(new DataHandler(ds));
            contenido.setFileName(file);
            contenido.addHeader("content-type", contentType);
            contenido.addHeader("content-transfer-encoding", strFileEncodingType);
            contenido.setContentID("<multimedia1>");

            // Agrego el Bodypart al Multipart
            mmpContenido.addBodyPart(contenido);
            //######################################################################
            //######################################################################

            //######################################################################
            // 				Agrego contenidos de texto al Multipart
            //######################################################################
            if (strText != null) {
                contenido = new MimeBodyPart();

                contenido.setText(strText);
                contenido.addHeader("content-type", "text/plain");
                contenido.setContentID("<text_part>");

                // Agrego el Bodypart al Multipart
                mmpContenido.addBodyPart(contenido);
            }
            //######################################################################
            //######################################################################

            // Encapsulo el Multipart de contenido en un Bodypart
            contenido = new MimeBodyPart();
            contenido.setContent(mmpContenido);
            contenido.addHeader("Content-Type", mmpContenido.getContentType());
            contenido.setContentID("<" + SOAPMessage.getContentID() + ">");

            // Agrego el Multipart de contenido como parte del Multipart
            multiPart.addBodyPart(contenido);

            HashMap arr = parseContentType(multiPart.getContentType());
            ParameterList parameters = (ParameterList) arr.get("parameterList");

            strContentType += "boundary=\"" + parameters.get("boundary") + "\"";

            connection.setRequestProperty("Content-Type", strContentType);
            multiPart.writeTo(System.out);

            DataOutputStream printout = new DataOutputStream(connection.getOutputStream());

            multiPart.writeTo(printout);
            printout.flush();
            printout.close();

            DataInputStream is = new DataInputStream(connection.getInputStream());

            // Get response data.
            String str;
            while (null != ((str = is.readLine()))) {
                System.out.println(str);
            }
            is.close();

            return "Ok";

        } catch (MalformedURLException e) {
            e.printStackTrace();
            throw new MM7SubmitRequestException(e.getMessage());
        } catch (IOException e) {
            e.printStackTrace();
            throw new MM7SubmitRequestException(e.getMessage());
        } catch (MessagingException e) {
            e.printStackTrace();
            throw new MM7SubmitRequestException(e.getMessage());
        } catch (SOAPException e) {
            e.printStackTrace();
            throw new MM7SubmitRequestException(e.getMessage());
        }
    }

    /*
    private static String getStringFromDocument(String ns, Document doc) {
    String xml = null;
    try {
    XMLOutputter outputter = new XMLOutputter();
    StringWriter writer = new StringWriter();
    outputter.output(doc, writer);

    writer.close();

    xml = writer.toString();

    if (ns != null){
    xml = xml.replaceAll("<Body", "<" + ns + ":Body");
    xml = xml.replaceAll("Body>", ns + ":Body>");
    xml = xml.replaceAll("<Header", "<" + ns + ":Header");
    xml = xml.replaceAll("Header>", ns + ":Header>");
    xml = xml.replaceAll("<Envelope xmlns", "<" + ns + ":Envelope xmlns:" + ns);
    xml = xml.replaceAll("Envelope>", ns + ":Envelope>");
    xml = xml.replaceAll("mustUnderstand", ns + ":mustUnderstand");
    xml = xml.replaceAll(" xmlns=\"\"", "");
    }

    } catch (IOException e) {
    // TODO Auto-generated catch block
    e.printStackTrace();
    }
    return xml;
    }
     */
    public static HashMap parseContentType(String s) throws ParseException {
        ParameterList list = null;
        HashMap map = new HashMap();

        HeaderTokenizer headertokenizer = new HeaderTokenizer(s, "()<>@,;:\\\"\t []/?=");
        HeaderTokenizer.Token token = headertokenizer.next();

        if (token.getType() != -1) {
            throw new ParseException();
        }

        String primaryType = token.getValue();
        map.put("primaryType", primaryType);

        token = headertokenizer.next();
        if ((char) token.getType() != '/') {
            throw new ParseException();
        }

        token = headertokenizer.next();

        if (token.getType() != -1) {
            throw new ParseException();
        }

        String subType = token.getValue();
        map.put("subType", subType);

        String s1 = headertokenizer.getRemainder();

        if (s1 != null) {
            list = new ParameterList(s1);
            map.put("parameterList", list);
        }

        return map;
    }

    public static String enviarMMS(String path, String file, String strEncodingType, String recipient, String text, String subject, String shortCode, String contentType) {
        String userName = null;
        String password = null;
        String mmscUrl = null;
        String messageId = null;

        try {
            // read props file for mmscurl, username and password
            Properties props = new Properties();
            props.load(SimpleSender.class.getResourceAsStream("/resources/SimpleSender.properties"));
            mmscUrl = props.getProperty("mmscurl");
            userName = props.getProperty("username");
            password = props.getProperty("password");

        } catch (IOException ioe) {
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

        try {
            messageId = sendMessage(mmscUrl, userName, password, subject, recipient, text, path, file, contentType, strEncodingType, shortCode);

        } catch (MM7SubmitRequestException e) {
            e.printStackTrace();
        }

        return messageId;
    }
}
