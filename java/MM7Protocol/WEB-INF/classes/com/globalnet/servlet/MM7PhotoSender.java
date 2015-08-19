package com.globalnet.servlet;

import globalnet.img.ImageConv;

import javax.servlet.http.*;
import javax.servlet.*;

import com.globalnet.mm7.MM7SubmitRequest;

import java.io.*;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Properties;
import java.util.StringTokenizer;

public class MM7PhotoSender extends HttpServlet {

	private String path;
	private String formato;
	private String celular;
	private String text;
	private String subject;
	private String pathlocal;
	private String encodingType;
	private String shortCode;

	public void doGet(HttpServletRequest req, HttpServletResponse res) throws ServletException, IOException {
	   Date now = new Date();
       SimpleDateFormat format = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
       String fecha = format.format(now);
       System.out.println("====================== INICIO ** " + fecha + " ** INICIO ======================");
		
		// Leo los parametros recibidos
		path = req.getParameter("path");
		formato = req.getParameter("formato");
		celular = req.getParameter("celular");
		text = req.getParameter("text");
		subject = req.getParameter("subject");
		encodingType = req.getParameter("encodingType");
		shortCode = req.getParameter("shortCode");
		
		if (encodingType == null){
			encodingType = "binary";
		} 
		if (subject == null){
			subject = "GlobalNet";
		} 
		
		Properties props = new Properties();
        try {        	
            props.load(MM7PhotoSender.class.getResourceAsStream("/resources/SimpleSender.properties"));
            
        } catch( IOException ioe ) {
            System.out.println("IOException reading properties file: " + ioe.getMessage());
            System.exit(1);
        }		
        pathlocal = props.getProperty("pathlocal");
		
		// Determino que hacer luego de enviado el MMS
		// en funcin de la app
		StringTokenizer st = new StringTokenizer(path, "/");
			
		String archivo = "";
		while(st.hasMoreTokens()){
			archivo = st.nextToken();	
		}

		String ext = path.substring(path.lastIndexOf(".") + 1);
		
		File f = new File(path);
		File file = new File(pathlocal + archivo);
		
		copy(f, file);
		
		if (formato != null && !formato.equalsIgnoreCase(ext)){
			// Es necesario convertir entre formatos
			if (file.exists() && file.length() > 0){
				String archivoGif = archivo.substring(0, archivo.lastIndexOf(".")) + ".gif";
				ImageConv.toGif(pathlocal + archivo, pathlocal + archivoGif);
				archivo = archivoGif;
			}
		}
		
		String messageId = null;
		if (file.exists() && file.length()>0){
			// Envio el contenido por MMS
			messageId = MM7SubmitRequest.enviarMMS(pathlocal, archivo, encodingType, celular, text, subject, shortCode, "image");
		}
		
		PrintWriter out = res.getWriter();		
		if (messageId != null) {
			out.println("true");
		} else {
			out.println("false");
		}
		out.close();
		
		file.delete();
		
		System.out.println("====================== FIN ** " + fecha + " ** FIN ======================");

	}
	
    private void copy(File src, File dst) throws IOException {
        InputStream in = new FileInputStream(src);
        OutputStream out = new FileOutputStream(dst);
    
        byte[] buf = new byte[1024];
        int len;
        while ((len = in.read(buf)) > 0) {
            out.write(buf, 0, len);
        }
        in.close();
        out.close();
    }	

}
