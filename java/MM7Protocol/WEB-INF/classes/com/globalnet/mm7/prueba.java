package com.globalnet.mm7;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;

import javax.activation.DataHandler;
import javax.activation.FileDataSource;
import javax.mail.MessagingException;


import javax.mail.internet.HeaderTokenizer;
import javax.mail.internet.MimeBodyPart;
import javax.mail.internet.MimeMultipart;
/*
import com.globalnet.mail.MimeBodyPart;
import com.globalnet.mail.MimeMultipart;
*/
import javax.mail.internet.ParameterList;
import javax.mail.internet.ParseException;

public class prueba {

	public static void main(String[] args){
		
		try {
			
				MimeMultipart multipart = new MimeMultipart();
				
					MimeBodyPart body1 = new MimeBodyPart();
					body1.setContentID("cid_xxxxxxxxxxxxxxx");
			
/*
Enumeration e = body1.getAllHeaders();
while (e.hasMoreElements()){
	Header h = (Header) e.nextElement();
	System.out.println(h.getValue());
}
*/
			
					MimeMultipart multipart2 = new MimeMultipart("related");
					MimeBodyPart body2 = new MimeBodyPart();

					FileDataSource ds = new FileDataSource("cel.gif");
					body2.setDataHandler(new DataHandler(ds));			
					body2.setFileName("cel.gif");
					body2.addHeader("content-type", "image/gif");

					
					multipart2.addBodyPart(body2);
					
					body1.setContent(multipart2);
		
				multipart.addBodyPart(body1);
			
				HashMap arr = parseContentType(multipart.getContentType());
				ParameterList parameters = (ParameterList) arr.get("parameterList");
				System.out.println(parameters.get("boundary"));
				
			
FileOutputStream fs = new FileOutputStream("pepe.txt");
multipart.writeTo(fs);
fs.close();

		} catch (MessagingException e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
		} catch (FileNotFoundException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
	}
	
	
	
	
	
    public static HashMap parseContentType(String s) throws ParseException {
    	ParameterList list = null;
    	HashMap map = new HashMap();
    	
	    HeaderTokenizer headertokenizer = new HeaderTokenizer(s, "()<>@,;:\\\"\t []/?=");
	    HeaderTokenizer.Token token = headertokenizer.next();
	    
	    if(token.getType() != -1)
	        throw new ParseException();

	    String primaryType = token.getValue();
	    map.put("primaryType", primaryType);

	    token = headertokenizer.next();
	    if((char)token.getType() != '/')
	        throw new ParseException();
	    
	    token = headertokenizer.next();
	    
	    if(token.getType() != -1)
	        throw new ParseException();
	    
	    String subType = token.getValue();
	    map.put("subType", subType);
	    
	    String s1 = headertokenizer.getRemainder();
	    
	    if(s1 != null){
	    	list = new ParameterList(s1);
	    	map.put("parameterList", list);
	    }
	    
	    return map;
	}

	
}
