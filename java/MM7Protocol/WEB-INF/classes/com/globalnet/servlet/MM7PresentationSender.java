package com.globalnet.servlet;

import globalnet.ftp.TransferFTP;
import globalnet.ftp.exceptions.TransferFTPException;
import globalnet.ftp.exceptions.TransferFTPParameterException;
import globalnet.img.ImageConv;

import javax.servlet.http.*;
import javax.servlet.*;

import com.globalnet.mm7.MM7SubmitRequest;
import com.globalnet.mm7.SubmitMMS;

import java.io.*;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.Properties;
import java.util.StringTokenizer;

public class MM7PresentationSender extends HttpServlet {

	private long idref = -1;
	private String path;
	private String celular;
	private String subject;
	private String text;
	private String pathlocal;
	private String encodingType;
	private String shortCode;
	private String nombreTablaSalida = "";
	private String nombreTablaFrames = "";
	private String strHTTPMessage = "";

	public void doGet(HttpServletRequest req, HttpServletResponse res) throws ServletException, IOException {
		Date now = new Date();
		SimpleDateFormat format = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
		String fecha = format.format(now);
		System.out.println("====================== INICIO ** " + fecha + " ** INICIO ======================");
		
		try {
			// Leo los parametros recibidos
			String strIdref = req.getParameter("idref");
			if (strIdref != null)
				idref = Long.parseLong(strIdref);
			
			if (encodingType == null){
				encodingType = "base64";
			}
			
			if (subject == null){
				subject = "Composer";
			}
			
			Properties props = new Properties();
	        try {        	
	            props.load(MM7PresentationSender.class.getResourceAsStream("/resources/SimpleSender.properties"));
	            
	        } catch(IOException ioe) {
	            System.out.println("IOException reading properties file: " + ioe.getMessage());
	            System.exit(1);
	        }
	
	        pathlocal = props.getProperty("pathlocal");
	        nombreTablaSalida = props.getProperty("nombre_tabla_composer_salida");
	        nombreTablaFrames = props.getProperty("nombre_tabla_composer_frames");
	
			// Determino que hacer luego de enviado el MMS
			// en funcin de la app
			String connString = null;
			String messageId = null;
	
			connString = props.getProperty("mm7_mysql");
			
			if (connString != null && idref > -1) {
				try {
					Class.forName("com.mysql.jdbc.Driver").newInstance();
					
				}catch (Exception e) {
					System.out.println("ERROR en mysql driver: " + e.getMessage());
				}
	
				try {
					Connection conn = DriverManager.getConnection(connString);
					
					String sql = "SELECT * FROM " + nombreTablaSalida + " WHERE id = ?";
					PreparedStatement pstmt = conn.prepareStatement(sql);
					pstmt.setLong(1, idref);
					ResultSet rsq = pstmt.executeQuery();
					rsq.first();

					String strApp = rsq.getString("app");
//					String strRemitente = rsq.getString("remitente");
					String strDestinatario = rsq.getString("destinatario");
					int intTipoCel = rsq.getInt("tipocel");
					long lngIdSonido = rsq.getLong("id_sonido");
					int intDuracionSonido = rsq.getInt("duracion_sonido");
					
					//===================================================================
					//	Armado de los array con los contenidos del mensaje
					//===================================================================
					ArrayList contenidos = new ArrayList();
		        	ArrayList textos = new ArrayList();
		        	ArrayList duraciones = new ArrayList();

		        	sql = "SELECT * FROM " + nombreTablaFrames + " WHERE id_contenido>0 AND id = ? ORDER BY indice asc";		        	
					pstmt = conn.prepareStatement(sql);
					pstmt.setLong(1, idref);
					rsq = pstmt.executeQuery();
					while (rsq.next()) {
						contenidos.add(new Long(rsq.getLong("id_contenido")));
						textos.add(rsq.getString("texto"));
						duraciones.add(new Long(rsq.getLong("tiempo")));
					}
					//===================================================================
					//===================================================================
					
					messageId = SubmitMMS.enviarMMS(pathlocal, encodingType, strDestinatario, subject, strApp, lngIdSonido, intDuracionSonido, intTipoCel, contenidos, textos, duraciones);
					
				} catch (SQLException ex) {
					System.out.println("SQLException: " + ex.getMessage());
					System.out.println("SQLState: " + ex.getSQLState());
					System.out.println("VendorError: " + ex.getErrorCode());
				}
			}

			if (messageId != null) {
				strHTTPMessage = "OK: " + path + " | " + messageId;
			} else {
				strHTTPMessage = "ERROR: " + path;
			}

		}catch(NumberFormatException e) {
			strHTTPMessage = "ERROR: " + e.getMessage();
		}
		
		PrintWriter out = res.getWriter();
		out.println(strHTTPMessage);
		out.close();
		
		System.out.println("====================== FIN ** " + fecha + " ** FIN ======================");

	}
	
	public static void main(String args[]) {
		
	}

}
