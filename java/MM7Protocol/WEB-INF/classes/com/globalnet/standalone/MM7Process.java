package com.globalnet.standalone;

import globalnet.ftp.*;
import globalnet.ftp.exceptions.TransferFTPException;
import globalnet.ftp.exceptions.TransferFTPParameterException;

import java.awt.Image;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.URL;
import java.net.URLConnection;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.Properties;
import java.util.StringTokenizer;

import javax.mail.BodyPart;
import javax.mail.MessagingException;
import javax.mail.internet.MimeMultipart;

import com.sun.jimi.core.Jimi;
import com.sun.jimi.core.JimiException;

public class MM7Process {
	String subject = null;
	String texto = null;
	String tipo = "";
	String filename = "";
	String operadora = "";

	String application = null;
	long idref = 0;
	BodyPart bodyPart = null;
	InputStream in;
	byte[] buf;
	int len;
	OutputStream out;

	long bodySize = 0;
	String nroCelular = "";
	String pathserver = "";
	String connString = "";

	String ftpHost = "";
	String ftpUser = "";
	String ftpUserPass = "";
	String ftpPath = "";

	boolean blnUsarFTP = false;
	String nombreTabla = "";
	String prefijo = "";

	public MM7Process() {
		Properties props = new Properties();
		try{
			props.load(MM7Process.class.getResourceAsStream("/resources/SimpleSender.properties"));

		}catch(IOException ioe) {
			System.out.println( "IOException reading properties file: " + ioe.getMessage() );
		}

		prefijo = props.getProperty("prefijo");
		connString = props.getProperty("mm7_mysql");
		
		if(this.application == props.getProperty("BlogShortCode")){
			pathserver = props.getProperty("pathlocal_blog");
			nombreTabla = props.getProperty("nombre_tabla_entrada_blog");
		} else {
			pathserver = props.getProperty("pathlocal");
			nombreTabla = props.getProperty("nombre_tabla_entrada");	
		}
		
		
		this.operadora = props.getProperty("operadora");
		String strUsarFTP = props.getProperty("usar_ftp");
		if (strUsarFTP != null){
			blnUsarFTP = (Boolean.valueOf(strUsarFTP)).booleanValue();
		}

		if (blnUsarFTP) {
			ftpUser = props.getProperty("usa_ftpuser");
			ftpUserPass = props.getProperty("usa_ftppass");
			ftpHost = props.getProperty("usa_ftphost");
			ftpPath = props.getProperty("usa_ftpPath");
			connString = props.getProperty("usa_mysql");
		}

		prefijo = props.getProperty("prefijo");
	}

	public void processMultipart(MimeMultipart mmpPart) {
		String strCategory = null;

		if (nroCelular.equals("")) {
			System.out.println("No se procesa el mensaje por falta de remitente");
		}else{
			try{
				for (int k = 0; k < mmpPart.getCount(); k++) {
//					String ctype[] = mmpPart.getBodyPart(k).getHeader("content-type");

					String contentType = mmpPart.getBodyPart(k).getContentType();
					String ct = getAttribute(1, contentType);

					if (! ct.equalsIgnoreCase("application/smil")) {
						if (bodyPart == null) {
							filename = getValue(contentType, "name");

							System.out.println("ct=" + ct);
							System.out.println("filename=" + filename);

							bodyPart = mmpPart.getBodyPart(k);
							in = bodyPart.getInputStream();
							buf = new byte[1024];

							while ((len = in.read(buf)) > 0) {
								bodySize += len;
							}

							System.out.println(bodySize + "bytes");


							/*
StringTokenizer st = new StringTokenizer(ct, "/");
strCategory = st.nextToken();
nombreTabla = strCategory + "_" + nombreTabla;
							 */

							if (ct.equalsIgnoreCase("image/gif") || ct.equalsIgnoreCase("image/jpeg") || ct.equalsIgnoreCase("image/png")) {
								strCategory = "image";

								if (ct.equalsIgnoreCase("image/gif")) {
									tipo = "gif";
								}

								if (ct.equalsIgnoreCase("image/jpeg")) {
									tipo = "jpg";
								}

								if (ct.equalsIgnoreCase("image/png")) {
									tipo = "png";
								}

								// Controlo tamao 200kb
								if (bodySize > 204800) {
									System.out.println("ERROR: Foto demasiado grande");
									bodyPart = null;
								}

							} else if (ct.equalsIgnoreCase("video/3gp") || ct.equalsIgnoreCase("video/3gpp")) {
								tipo = "3gp";
								strCategory = "video";

							} else {
								bodyPart = null;
							}

							if (strCategory != null)
								nombreTabla = strCategory + "_" + nombreTabla;
						}

						if (texto == null) {
							if (mmpPart.getBodyPart(k).getContentType().equalsIgnoreCase("text/plain")) {
								texto = (String) mmpPart.getBodyPart(k).getContent();
								int largo = texto.length();
								if (largo > 255) {
									texto = texto.substring(0, 254);
								}
								System.out.println("TEXTO: " + texto);
							}
						}
					}
				}

				if (bodyPart != null) {
					Class.forName("com.mysql.jdbc.Driver").newInstance();
					Connection conn = DriverManager.getConnection(connString);

					if (texto == null) {
						texto = "";
					}

					if (nroCelular.indexOf(this.prefijo) == 0) {
						nroCelular = nroCelular.substring(this.prefijo.length(), nroCelular.length());
					}

					// Controlo quota 15 Mb
					/*String sql = "SELECT SUM(size) AS quota FROM " + nombreTabla + " WHERE celular = ?";
					System.out.println("DEBUG: " + sql);
					PreparedStatement pstmt = conn.prepareStatement(sql);
					pstmt.setString(1, nroCelular);
					ResultSet rsq = pstmt.executeQuery();
					rsq.first();
					long diskusage = rsq.getLong("quota");
					System.out.println("DEBUG_ QUOTA: " + diskusage);
					if ((diskusage + bodySize) >= 15360000) { // Excede quota
						System.out.println("ERROR: Quota del usuario excedida");

					}else{ // No excede quota
*/
						String sql = "";
						System.out.println("nroCelular=" + nroCelular);
						System.out.println("subject=" + subject);
						System.out.println("texto=" + texto);
						System.out.println("filename=" + filename);
						System.out.println("fotosize=" + bodySize);
						System.out.println("application=" + application);

						if (subject != null) {
							subject = subject.trim();
						}

						sql = "INSERT INTO " + nombreTabla + "(celular, cabezal, fecha, descripcion, nombre, size, subida_desde, fecha_alta, hora_alta, app) ";
						sql += "VALUES('" + nroCelular + "', '" + (subject == null ? "": subject) + "', CURDATE(), '" + texto + "', '" + filename + "', " + bodySize + ", 'mms', CURDATE(), CURTIME(), '" + application + "') ";
						System.out.println("DEBUG: " + sql);
						Statement stmt = conn.createStatement();

						/*	
							Metodo sugerido para estos casos
							http://ftp.tuniv.szczecin.pl/pub/mysql/doc/refman/5.0/es/cj-retrieve-autoinc.html
						 */
						stmt.execute(sql, Statement.RETURN_GENERATED_KEYS);
						ResultSet rs = stmt.getGeneratedKeys();
						if (rs != null) {
							rs.first();
							idref = rs.getLong(1);
						}else{
							System.out.println("No se genero el registro en la tabla fotos");
						}


						
	                     //###################################################
	                    //	Chequear si corresponden notificaciones SMS
	                    //###################################################
	                    System.out.println("### ENVIO DE SMS ###");
	                    URLConnection urlConn;
	                    Properties props = new Properties();
	                    props.load(MM7Process.class.getResourceAsStream("/resources/SimpleSender.properties"));
	                    String usrSms = props.getProperty("usrSms");
	                    String celularEnvio = nroCelular.replace("+", "");
	                    String url_envio = "http://10.0.0.243/smsc/classes/mm7/invocacion_desde_tomcat_241.php?usuario=" + usrSms + "&shortcode_mms=" + application + "&celular=" + celularEnvio + "&tipo_contenido=" + strCategory;
	                    System.out.println("URL envio SMS: " + url_envio);
	                    URL fixer2 = new URL(url_envio);
	                    try {
	                        urlConn = fixer2.openConnection();
	                        urlConn.connect();
	                        System.out.println("Content-length: " + urlConn.getContentLength());
	                    } catch (Exception e){
	                        System.out.println("ERROR: " + e.getMessage());
	                    }
	                    System.out.println("### FIN ENVIO DE SMS ###");
	                    //###################################################
	                    //###################################################


						String pathOrig = null;
						String pathLocal = null;

						pathOrig = pathserver + "entrada/" + strCategory + "/" + idref + "." + tipo;
						if (strCategory.equalsIgnoreCase("image")) {
							if (!tipo.equals("jpg")) {
								pathOrig = pathserver + "tmp/" + idref + "." + tipo;
								pathLocal = pathserver + "entrada/" + strCategory + "/" + idref + ".jpg";
							}
						}

						in = bodyPart.getInputStream();

						out = new FileOutputStream(pathOrig);

						// Transfer bytes from in to out
						buf = new byte[1024];
						while ((len = in.read(buf)) > 0) {
							out.write(buf, 0, len);
						}
						in.close();
						out.close();

						if (strCategory.equalsIgnoreCase("image")) {

							// Ver si es GIF y convertir a JPG
							if (!tipo.equals("jpg")) {
								try {
									Image image = Jimi.getImage(pathOrig);
									Jimi.putImage(image, pathLocal);
									File f = new File(pathLocal);
									bodySize = f.length();

									// Actualizo el tamao luego de convertir a JPEG
									sql = "UPDATE " + nombreTabla + " SET size='" + bodySize + "' WHERE id=" + idref;
									System.out.println("DEBUG: " + sql);
									stmt.execute(sql);

								} catch (JimiException je) {
									System.out.println(je.getMessage());
								}

								File file = new File(pathOrig);
								file.delete();
								/*
							}else{
								File file = new File(pathOrig);
								file.renameTo(new File(pathLocal));
								 */
							}
							
							// Patch by Leon & eltin, at 22/02/08
							// Invoke URL in order to fix corrupted images.
							URL fixer = new URL("http://10.0.0.250/image_fix/fix.php?operadora=" + this.operadora + "&id=" + idref);
							fixer.openConnection();
							// May the hackzor be with you
						}

						if (blnUsarFTP) {
							String pathRemoto = ftpPath + idref + ".jpg";
							TransferFTP tftp;
							try {	
								tftp = new TransferFTP(ftpHost, ftpUser, ftpUserPass, pathRemoto, pathLocal, true, true);
								tftp.transfer();

							}catch(TransferFTPParameterException tfpe) {
								System.out.println(tfpe.getMessage());
							}catch(TransferFTPException tfe) {
								System.out.println(tfe.getMessage());
							}
						}
					//}
				}

			}catch(MessagingException me) {
				System.out.println(me.getMessage());
			}catch(IOException ioe) {
				System.out.println(ioe.getMessage());
			}catch(SQLException ex) {
				// handle any errors
				System.out.println("SQLException: " + ex.getMessage());
				System.out.println("SQLState: " + ex.getSQLState());
				System.out.println("VendorError: " + ex.getErrorCode());
			}catch(InstantiationException ie) {
				System.out.println(ie.getMessage());
			}catch(IllegalAccessException iae) {
				System.out.println(iae.getMessage());
			}catch(ClassNotFoundException cnfe) {
				System.out.println(cnfe.getMessage());
			}
		}
	}

	private String getAttribute(int field, String contentType) {
		String value = null;
		StringTokenizer st = new StringTokenizer(contentType, ";");
		for (int i = 0; i < field; i++) value = (String) st.nextToken();
		return value;
	}

	private String getValue(String source, String key) {
		String fieldName = null;
		String fieldValue = null;

		StringTokenizer st = new StringTokenizer(source, ";");
		while (st.hasMoreTokens()){
			String strAux = st.nextToken();
			StringTokenizer stField = new StringTokenizer(strAux, "=");
			if (stField.countTokens() == 2){
				fieldName = (stField.nextToken()).trim();
				fieldValue = (stField.nextToken()).trim();

				if (fieldName.equalsIgnoreCase(key)){
					break;
				}
			}
		}

		return fieldValue;
	}

	public void setApplication(String application) {
		this.application = application;
	}

	public void setSubject(String subject) {
		this.subject = subject;
	}

	public void setNroCelular (String cel){
		nroCelular = cel.replaceAll("\\+", "");
		if (prefijo != null && nroCelular != null){
			if (nroCelular.substring(0, prefijo.length()).equals(prefijo)){
				nroCelular = nroCelular.substring(prefijo.length()); 
			}
		}
	}

	public long getIdref() {
		return idref;
	}

}
