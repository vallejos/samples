package com.globalnet.util;

import java.io.DataInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;

public class HTTPGetter {

	public static void get(String strURL) throws IOException{

		HttpURLConnection connection = null;
		DataInputStream input = null;
		String str = null;
		
		URL url = new URL(strURL);
		connection = (HttpURLConnection) url.openConnection();
		connection.setRequestMethod("GET");
		
		connection.setDoInput(true);
		connection.setDoOutput(true);
		
		connection.connect();
		
		InputStream mensaje = connection.getInputStream();
		if (mensaje != null){
			input = new DataInputStream(mensaje);
			while (null != ((str = input.readLine()))) {
			}
			input.close ();
		}
		
		InputStream error = connection.getErrorStream();
		if (error != null){
			input = new DataInputStream(error);
			while (null != ((str = input.readLine()))) {
			}
			input.close ();
		}

		connection.disconnect();

	}
	
}
