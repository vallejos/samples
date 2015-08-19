package com.globalnet.mm7;

import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStream;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.Properties;
import java.util.StringTokenizer;

import javax.activation.DataHandler;
import javax.activation.FileDataSource;
import javax.mail.MessagingException;
import javax.mail.internet.HeaderTokenizer;
import javax.mail.internet.MimeBodyPart;
import javax.mail.internet.MimeMultipart;
import javax.mail.internet.ParameterList;
import javax.mail.internet.ParseException;
import javax.xml.soap.SOAPException;

import org.jdom.Document;

import com.globalnet.servlet.Base64;
import com.globalnet.standalone.SimpleSender;

public class SubmitMMS {
	public static final String textContentID = "{TEXT_CONTENT_ID}";
	public static final String textContentIDReg = "\\{TEXT_CONTENT_ID\\}";
	public static final String audioContentID = "{AUDIO_CONTENT_ID}";
	public static final String audioContentIDReg = "\\{AUDIO_CONTENT_ID\\}";
	public static final String imageContentID = "{IMAGE_CONTENT_ID}";
	public static final String imageContentIDReg = "\\{IMAGE_CONTENT_ID\\}";
	public static final String segContentDuration = "{CONTENT_DURATION}";
	public static final String segContentDurationReg = "\\{CONTENT_DURATION\\}";
	
	public static final String archivoRemoverReg = "/netuy";
	
	public static String sendMessage(String mmscUrl, String userName, String password, String subject, 
									 String recipient, long idSonido, long duracionSonido, ArrayList contenidos, ArrayList textos, 
									 ArrayList duraciones, String strFileEncodingType, long lngTipoCelular, 
									 String strShortCode) throws MM7SubmitRequestException {
		String strSMILBodySeq = "";
		
		String strSMIL = "";
		String strSMILHead = "";
		String strSMILBodyHead = "";
		String strSMILBodySeqPart = "";
		String strSMILBodyFoot = "";
		String strSMILFoot = "";
		
		String strContentType = null;
		String hostIP = null;
		String strNameSpace = null;
		String strVASPID = "2002";
		String strVASID = "2002";
		String strPathContenidos = null;
		String strPathLocal = null;
		String nombreTablaContenidos = "contenidos";
		String connString = "";
		
		boolean addTransactionID = false;
		boolean addHeaderNamespace = false;
		boolean addBodyNamespace = false;
		boolean useAuthentication = false;
		boolean debug = false;
		
		try {
			try {
	        	Properties props = new Properties();
				props.load(SubmitMMS.class.getResourceAsStream("/resources/SimpleSender.properties"));
		    	hostIP = props.getProperty("host_ip");
		    	strNameSpace = props.getProperty("ns");
		    	
				strVASPID = props.getProperty("VASPID");
				strVASID = props.getProperty("VASID");
				if (strShortCode == null)
					strShortCode = props.getProperty("ShortCode");
				
				strPathLocal = props.getProperty("pathlocal");
				strPathContenidos = props.getProperty("path_contenidos");
				connString = props.getProperty("Web_mysql");

				addTransactionID = props.getProperty("addTransactionID") == null || props.getProperty("addTransactionID").equalsIgnoreCase("False") ? false: true;
				addHeaderNamespace = props.getProperty("addHeaderNamespace") == null || props.getProperty("addHeaderNamespace").equalsIgnoreCase("False") ? false: true;
				addBodyNamespace = props.getProperty("addBodyNamespace") == null || props.getProperty("addBodyNamespace").equalsIgnoreCase("False") ? false: true;
				useAuthentication = props.getProperty("authentication") == null || props.getProperty("authentication").equalsIgnoreCase("False") ? false: true;
				debug = props.getProperty("debug") == null || props.getProperty("debug").equalsIgnoreCase("False") ? false: true;
		    	
			}catch(IOException e){
			}
			
			URL url = new URL(mmscUrl);
			URLConnection connection = url.openConnection();
			connection.setDoInput(true);
			connection.setDoOutput(true);

			String strContentId = "<content_id>";
			
			if (hostIP != null)
				connection.setRequestProperty("Host", hostIP);
			
			connection.setRequestProperty("Connection", "keep-alive");
			connection.setRequestProperty("SOAPAction", "\"\"");
			
			if (useAuthentication){
				String strBase64 = Base64.encodeString(userName + ":" + password, false);
				connection.setRequestProperty("Authorization", " Basic " + strBase64);
			}
			
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
			
			MimeBodyPart contenidoSmil = null;
			//###########################################################################################
			//###########################################################################################
			//		recorre los id de contenidos para agregar las partes al mimeMultiPart
			//###########################################################################################
			//###########################################################################################
			try {
				int largo = 62;
				int ancho = 62;
				
				Class.forName("com.mysql.jdbc.Driver").newInstance();
				Connection connWeb = DriverManager.getConnection(connString);
				
				//#################################################################
				//				obtener resolucion del celular
				//#################################################################
				PreparedStatement ps = connWeb.prepareStatement("SELECT largo, ancho FROM pantalla WHERE celular = ?");
				ps.setLong(1, lngTipoCelular);
				ResultSet rs = ps.executeQuery();
				rs.first();
				
				largo = rs.getInt("largo");
				ancho = rs.getInt("ancho");
				
				//#################################################################
				//#################################################################
				
				//#################################################################
				//			Definicion del smil final
				//#################################################################
				strSMILHead =  "<?xml version=\"1.0\"?>";
				strSMILHead += "<smil>";
				strSMILHead += "	<head>";
				strSMILHead += "		<layout>";
				strSMILHead += "			<root-layout width=\"" + ancho + "\" height=\"" + largo + "\" background-color=\"white\"/>";
				strSMILHead += "			<region id=\"texto\" left=\"5\" top=\"" + largo + "\" width=\"" + ancho + "\" height=\"80\" fit=\"hidden\"/>";
				strSMILHead += "			<region id=\"imagen\" left=\"0\" top=\"0\" width=\"" + ancho + "\" height=\"" + largo + "\" fit=\"hidden\"/>";
				strSMILHead += "			<region id=\"sonido\"/>";
				strSMILHead += "		</layout>";
				strSMILHead += "	</head>";
				
				strSMILBodyHead =  "	<body>";
				strSMILBodyHead += "		<par>";
				if (idSonido>0){
					strSMILBodyHead += "			<audio src=\"cid:audio-contentID\" region=\"sonido\" dur=\"" + segContentDurationReg + "\" />"; 
				}
				strSMILBodyHead += "			<seq>";
				
				strSMILBodySeqPart =  "				<par dur=\"" + segContentDuration + "s\">";
				strSMILBodySeqPart += "					<text src=\"cid:" + textContentID + "\" region=\"texto\"/>";
				strSMILBodySeqPart += "					<img src=\"cid:" + imageContentID + "\" region=\"imagen\"/>";
				strSMILBodySeqPart += "				</par>";
				
				strSMILBodyFoot =  "			</seq>";
				strSMILBodyFoot += "		</par>";
				strSMILBodyFoot += "	</body>";
				
				strSMILFoot  = "</smil>";
				
				contenidoSmil = new MimeBodyPart();
				//Agrego el Bodypart al Multipart
				mmpContenido.addBodyPart(contenidoSmil);
				
				String sql = "";
				PreparedStatement pstmt;
				ResultSet rsq;
				String strArchivo = "";
				FileDataSource ds;
				//######################################################################
				//######################################################################
				
				//######################################################################
				//					obtener el archivo de sonido
				//######################################################################
				if (idSonido>0){
					sql = "SELECT * FROM contenidos where id = ?";
					pstmt = connWeb.prepareStatement(sql);
					pstmt.setLong(1, idSonido);
					rsq = pstmt.executeQuery();
					rsq.first();
					
					strArchivo = rsq.getString("archivo");
					strArchivo = strArchivo.replaceAll(archivoRemoverReg, "");
					
					MimeBodyPart contenidoAudio = new MimeBodyPart();
					
					ds = new FileDataSource(strPathContenidos + strArchivo);
					contenidoAudio.setDataHandler(new DataHandler(ds));
					contenidoAudio.setFileName(strArchivo);
					contenidoAudio.addHeader("content-type", "audio/midi");
					contenidoAudio.addHeader("content-transfer-encoding", strFileEncodingType);
					contenidoAudio.setContentID("<audio-contentID>");
					
					strSMILBodyHead = strSMILBodyHead.replaceAll(segContentDurationReg, String.valueOf(duracionSonido));
				
					// Agrego el Bodypart al Multipart
					mmpContenido.addBodyPart(contenidoAudio);

				}
				//######################################################################
				//######################################################################
				
				Iterator itContenidos = contenidos.iterator();
				Iterator itTextos = textos.iterator();
				Iterator itDuraciones = duraciones.iterator();
				
				int i = 1;
				while (itContenidos.hasNext()) {
					long id_contenido = ((Long) itContenidos.next()).longValue();
					String strTexto = (String) itTextos.next();
					long segundos = ((Long) itDuraciones.next()).longValue();
					
					sql = "SELECT * FROM contenidos where id = ?";
					pstmt = connWeb.prepareStatement(sql);
					pstmt.setLong(1, id_contenido);
					
					rsq = pstmt.executeQuery();
					rsq.first();

					strArchivo = rsq.getString("archivo");
					strArchivo = strArchivo.replaceAll(archivoRemoverReg, "");
					
					String strImageContentId = "imagen_" + i;
					String strTextContentId = "text_" + i;
					
					//######################################################################
					// 				Agrego contenido multimedia al Multipart
					//######################################################################
					MimeBodyPart contenidoImage = new MimeBodyPart();
					
					strArchivo = strArchivo.replaceAll(imageContentIDReg, strImageContentId);
					
					ds = new FileDataSource(strPathContenidos + strArchivo);
					contenidoImage.setDataHandler(new DataHandler(ds));
					contenidoImage.setFileName(strArchivo);
					contenidoImage.addHeader("content-type", "image/gif");
					contenidoImage.addHeader("content-transfer-encoding", strFileEncodingType);
					contenidoImage.setContentID("<" + strImageContentId + ">");
				
					// Agrego el Bodypart al Multipart
					mmpContenido.addBodyPart(contenidoImage);
					//######################################################################
					//######################################################################
					
					//######################################################################
					// 				Agrego contenido de texto al Multipart
					//######################################################################
					MimeBodyPart contenidoTexto = new MimeBodyPart();
					
					contenidoTexto.setText(strTexto);
					contenidoTexto.addHeader("content-type", "text/plain");
					contenidoTexto.setContentID("<" + strTextContentId + ">");
					
					// Agrego el Bodypart al Multipart
					mmpContenido.addBodyPart(contenidoTexto);
					//######################################################################
					//######################################################################
					
					String strSequencePart = new String(strSMILBodySeqPart);
					strSequencePart = strSequencePart.replaceAll(imageContentIDReg, strImageContentId);
					strSequencePart = strSequencePart.replaceAll(textContentIDReg, strTextContentId);
					strSequencePart = strSequencePart.replaceAll(segContentDurationReg, String.valueOf(segundos));
					strSMILBodySeq += strSequencePart;
					
					i++;
				}
				
			} catch (NumberFormatException e) {
				e.printStackTrace();
			} catch (InstantiationException e) {
				e.printStackTrace();
			} catch (IllegalAccessException e) {
				e.printStackTrace();
			} catch (ClassNotFoundException e) {
				e.printStackTrace();
			} catch (SQLException e) {
				e.printStackTrace();
			}
			
			//############################################################################
			//       Agrega el smil con la presentacion
			//############################################################################
			strSMIL = strSMILHead + strSMILBodyHead + strSMILBodySeq + strSMILBodyFoot + strSMILFoot;
			contenidoSmil.setText(strSMIL);
			
			contenidoSmil.addHeader("content-type", "application/smil");
			contenidoSmil.setContentID("<smil>");
			//############################################################################
			//############################################################################
			
			// Encapsulo el Multipart de contenido en un Bodypart
			MimeBodyPart contenido = new MimeBodyPart();
			contenido.setContent(mmpContenido);
			contenido.addHeader("Content-Type", mmpContenido.getContentType());

			contenido.setContentID("<" + SOAPMessage.getContentID() + ">");

			// Agrego el Multipart de contenido como parte del Multipart
			multiPart.addBodyPart(contenido);

			HashMap arr = parseContentType(multiPart.getContentType());
			ParameterList parameters = (ParameterList) arr.get("parameterList");

			strContentType += "boundary=\"" + parameters.get("boundary") + "\"";

			connection.setRequestProperty("Content-Type", strContentType);
			
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
			
			if (debug) {
				OutputStream pepe = new FileOutputStream(new File(strPathLocal + "composer_out.txt"));
				multiPart.writeTo(pepe);
			}
			
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

	public static HashMap parseContentType(String s) throws ParseException {
		ParameterList list = null;
		HashMap map = new HashMap();

		HeaderTokenizer headertokenizer = new HeaderTokenizer(s, "()<>@,;:\\\"\t []/?=");
		HeaderTokenizer.Token token = headertokenizer.next();

		if (token.getType() != -1)
			throw new ParseException();

		String primaryType = token.getValue();
		map.put("primaryType", primaryType);

		token = headertokenizer.next();
		if ((char) token.getType() != '/')
			throw new ParseException();

		token = headertokenizer.next();

		if (token.getType() != -1)
			throw new ParseException();

		String subType = token.getValue();
		map.put("subType", subType);

		String s1 = headertokenizer.getRemainder();

		if (s1 != null) {
			list = new ParameterList(s1);
			map.put("parameterList", list);
		}

		return map;
	}
	
    public static String enviarMMS(String path, String strEncodingType, String recipient, String subject, String shortCode, long idSonido, int intDuracionSonido, long lngTipoCel, ArrayList contenidos, ArrayList textos, ArrayList duraciones) {
        String userName = null;
        String password = null;
        String mmscUrl = null;
        String messageId = null;
        
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

        try {
			messageId = sendMessage(mmscUrl, userName, password, subject, recipient, idSonido, intDuracionSonido, contenidos, textos, duraciones, strEncodingType, lngTipoCel, shortCode);
			
        } catch (MM7SubmitRequestException e) {
			e.printStackTrace();
		}
        
        return messageId;
    }
	
}