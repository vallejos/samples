package com.globalnet.servlet;

import globalnet.ftp.TransferFTP;
import globalnet.ftp.exceptions.TransferFTPException;
import globalnet.ftp.exceptions.TransferFTPParameterException;
import globalnet.img.ImageConv;

import javax.servlet.http.*;
import javax.servlet.*;

import com.globalnet.mm7.MM7SubmitRequest;

import java.io.*;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.sql.Statement;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Properties;
import java.util.StringTokenizer;

public class MM7Sender extends HttpServlet {

	private String idref;
	private String path;
	private String formato;
	private String celular;
	private String subject;
	private String text;
	private String ftpuser;
	private String ftppass;
	private String ftphost;
	private String pathlocal;
	private String useLocalPath;
	private String encodingType;
	private String shortCode;
	private String contentType;
	private String nombreTabla = "";

	public void doGet(HttpServletRequest req, HttpServletResponse res) throws ServletException, IOException {
	   Date now = new Date();
       SimpleDateFormat format = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
       String fecha = format.format(now);
       System.out.println("====================== INICIO ** " + fecha + " ** INICIO ======================");
		
		// Leo los parametros recibidos
		path = req.getParameter("path");
		formato = req.getParameter("formato");
		idref = req.getParameter("idref");
		celular = req.getParameter("celular");
		text = req.getParameter("text");
		subject = req.getParameter("subject");
		encodingType = req.getParameter("encodingType");
		useLocalPath = req.getParameter("useLocalPath");
		shortCode = req.getParameter("shortCode");
		contentType = req.getParameter("contentType");
		
		if (encodingType == null){
			encodingType = "binary";
		} 
		if (subject == null){
			subject = "GlobalNet";
		}
		
		Properties props = new Properties();
        try {        	
            props.load( MM7Sender.class.getResourceAsStream("/resources/SimpleSender.properties") );
        } catch( IOException ioe ) {
            System.out.println( "IOException reading properties file: " + ioe.getMessage() );
            System.exit( 1 );
        }
        
        pathlocal = props.getProperty("pathlocal");
        nombreTabla = props.getProperty("nombre_tabla_salida");
        
        if (contentType != null) {
        	nombreTabla = contentType + "_" + nombreTabla;
        }
        
        // Determino que hacer luego de enviado el MMS
		// en funcin de la app
		String connString = null;

		connString = props.getProperty("usa_mysql");
		ftpuser = props.getProperty("usa_ftpuser");
		ftppass = props.getProperty("usa_ftppass");
		ftphost = props.getProperty("usa_ftphost"); 

		String archivo = "";
		if (useLocalPath == null || useLocalPath.equalsIgnoreCase("false")){
			StringTokenizer st = new StringTokenizer(path,"/");
			while(st.hasMoreTokens()){
				archivo = st.nextToken();	
			}
			
			// Obtengo el contenido del FTP para enviarlo
			TransferFTP tftp;
			try {
				tftp = new TransferFTP(ftphost, ftpuser, ftppass, path, pathlocal + archivo, true, false);
				tftp.transfer();
			} catch (TransferFTPParameterException tfpe) { 
				System.out.println(tfpe.getMessage());
			} catch (TransferFTPException tfe) {
				System.out.println(tfe.getMessage());
			}
		}else{
			StringTokenizer st = new StringTokenizer(path,"/");
			this.path = "";
			while(st.hasMoreTokens()){
				String strPart = st.nextToken();

				if (st.hasMoreTokens()){
					path += strPart + "/";
				}
				
				archivo = strPart;
			}
			pathlocal += path;
		}
		
		File f = null;
		
		if (contentType.equalsIgnoreCase("image")) {
			String ext = archivo.substring(archivo.lastIndexOf(".") + 1);
			f = new File(pathlocal + archivo);
			
			boolean convertImage = false;
			if (formato == null) { 
				if (!ext.equalsIgnoreCase("gif")) {
					convertImage = true;
				}
			}else if (!formato.equalsIgnoreCase(ext)) {
				convertImage = true;
			}
			
			if (convertImage) { 
				// Es necesario convertir entre formatos
				if (f.exists() && f.length()>0){
					String archivoGif = archivo.substring(0, archivo.lastIndexOf("."))+".gif";
					ImageConv.toGif(pathlocal + archivo, pathlocal + archivoGif);
					archivo = archivoGif;
				}
			}
		}
                PrintWriter out = res.getWriter();
		out.println("El archivo: " + pathlocal + archivo);
		f = new File(pathlocal + archivo);
		String messageId = null;
		out.println("Enviando... (i hope!)" + f.exists());
		if (f.exists() && f.length() > 0){
			// Envio el contenido por MMS
			messageId = MM7SubmitRequest.enviarMMS(pathlocal, archivo, encodingType, celular, text, subject, shortCode, contentType);
//			messageId = SimpleSender.enviarMMS(pathlocal + archivo, celular);
		} else {
			out.println("AAAAAAAAAAAAAAAAH");	
		}
		
			
		if (messageId != null) {
			out.println("OK: " + path + " | " + messageId);
		} else {
			out.println("ERROR: " + path);
		}
		out.close();
		
		if (connString != null && idref != null) {
			try {
				Class.forName("com.mysql.jdbc.Driver").newInstance();
			} catch (Exception e) {
				System.out.println("ERROR en mysql driver: " + e.getMessage());
			}

			try {
				Connection conn = DriverManager.getConnection(connString);
				Statement stmt = conn.createStatement();
				
				String condicionError = ", error=NULL";
				
				String condicionMessageId = ", MessageID= '" + messageId + "'";

				if (messageId == null) {
					condicionError = ", error='2'"; // Para marcar un problema al enviar
					condicionMessageId = ", MessageID=NULL";
				}
				
				String application = ", app='" + shortCode + "'";
				
				String sql = "UPDATE " + nombreTabla + " SET MMS = '1'" + condicionMessageId + condicionError + application + " WHERE id = " + idref;
				
				System.out.println(sql);
				stmt.execute(sql);				
			} catch (SQLException ex) {
				System.out.println("SQLException: " + ex.getMessage());
				System.out.println("SQLState: " + ex.getSQLState());
				System.out.println("VendorError: " + ex.getErrorCode());
			}
		}
		System.out.println("====================== FIN ** " + fecha + " ** FIN ======================");

	}

}
