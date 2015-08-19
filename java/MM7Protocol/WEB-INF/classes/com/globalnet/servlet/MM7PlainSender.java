package com.globalnet.servlet;

import java.io.IOException;
import java.net.URL;
import java.net.URLConnection;

import javax.activation.DataHandler;
import javax.activation.FileDataSource;
import javax.mail.MessagingException;
import javax.mail.internet.MimeBodyPart;
import javax.mail.internet.MimeMultipart;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.net.MalformedURLException;
import java.net.URLEncoder;
import java.util.Enumeration;

public class MM7PlainSender { //extends HttpServlet {

	protected void doGet(HttpServletRequest request,
			HttpServletResponse response) throws ServletException, IOException {

	}
/*
	protected void doPost(HttpServletRequest request,
			HttpServletResponse response) throws ServletException, IOException {
*/
	private static void dupost(){
		// parte que recibe post
		

		
		// parte que postea

		URL url;
		URLConnection urlConn;
		DataOutputStream printout;
		DataInputStream input;
		// URL of CGI-Bin script.
		try {
			
			// creo el multichancho
			
			MimeBodyPart mbp = new MimeBodyPart();
			MimeMultipart mmp = new MimeMultipart();

			mbp = new MimeBodyPart();
			String filename = "text.txt";
			FileDataSource source = new FileDataSource(filename);
			mbp.setDataHandler(new DataHandler(source));
			mbp.setFileName(filename);
			// mbp.addHeader("content-type", "text/plain");
			mmp.addBodyPart(mbp);

			mbp = new MimeBodyPart();
			filename = "cel.png";
			source = new FileDataSource(filename);
			mbp.setDataHandler(new DataHandler(source));			
			mbp.setFileName(filename);
			mbp.addHeader("content-type", "image/png");
			mbp.addHeader("content-transfer-encoding", "base64");
			mmp.addBodyPart(mbp);

			mbp = new MimeBodyPart();
			// Este es el multipart con los contenidos metido en un bodypart
			mbp.setContent(mmp);		
			
			// Este el el multipart que contiene al soap y al otro multipart
			mmp = new MimeMultipart();
			MimeBodyPart mbpsoap = new MimeBodyPart();
			String soap = "text.txt";
			mbpsoap.addHeader(soap, "text/xml");
			
			mbpsoap.addHeader("Content-Type","text/xml; charset=\"utf-8\"");
			mbpsoap.addHeader("Content-Transfer-Encoding","7bit");
			
			mmp.addBodyPart(mbpsoap);
			mmp.addBodyPart(mbp); // multipart con contenidos	
			
			// segun andres
			
			url = new URL("http://10.0.0.78:8080/proxy/proxy");
			// URL connection channel.

			urlConn = url.openConnection();			

			// Let the run-time system (RTS) know that we want input.
			urlConn.setDoInput(true);
			// Let the RTS know that we want to do output.
			urlConn.setDoOutput(true);

			//Setear headers
			//urlConn.setRequestProperty("MIME-Version","1.0");			
			urlConn.setRequestProperty("content-type","multipart/related");
				
			// Send POST output.
			printout = new DataOutputStream(urlConn.getOutputStream());

			mmp.writeTo(printout);
			printout.flush();
			printout.close();
			// Get response data.
			input = new DataInputStream(urlConn.getInputStream());
			String str;
			while (null != ((str = input.readLine()))) {
				System.out.println(str);
			}
			input.close();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (MessagingException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

	}
	public static void main(String args[]){
		dupost();
	}
}
