package com.globalnet.servlet;

import com.globalnet.standalone.MM7Process;
import com.globalnet.util.BufferedServletInputStream;
import com.globalnet.util.Filter;
import com.globalnet.util.FilterBilling;
import com.globalnet.util.HTTPGetter;
import com.globalnet.util.SMSSender;
import com.openwave.mms.mm7.*;
import com.openwave.mms.content.*;
import javax.mail.internet.*;
import javax.mail.*;
import javax.servlet.http.*;
import javax.servlet.*;
import java.io.*;
import java.util.*;
import java.text.DateFormat;

public class MM7Receiver extends HttpServlet implements MessageListener {
	
	private boolean blnDebug = false;

    public MM7Receiver(){}

    public void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
        try {
            RelayConnection.dispatch(request, response, this);
/*
            Properties props = new Properties();
            try{
            	props.load(MM7Receiver.class.getResourceAsStream("/resources/SimpleSender.properties"));
            	
                String strDebug = props.getProperty("debug");
                if (strDebug != null){
                	blnDebug = (Boolean.valueOf(strDebug)).booleanValue();
                }
                
                if (blnDebug) {
                	BufferedServletInputStream pepe = new BufferedServletInputStream(request.getInputStream());
                	
                	int intContentLength = request.getContentLength();
                	byte[] b = new byte[intContentLength];
                	int readedBytes = pepe.read(b, 0, intContentLength);
                	
                	System.out.write(b);
                }
            	
            }catch(IOException ioe) {
                System.out.println( "IOException reading properties file: " + ioe.getMessage() );
            }
*/
        }catch(APIException e) {
            e.printStackTrace();
            System.out.println("APIException dispatching message: " + e.getMessage() + "(" + e.getErrorCode() + ")");
        }
    }

    public void doGet (HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
        response.setContentType( "text/html" );
        PrintWriter writer = response.getWriter();

        writer.println("<html><head>");
        writer.println("<title>Sample MM7 Receiver Servlet</title>");
        writer.println("<META HTTP-EQUIV=\"Refresh\" CONTENT=\"10; URL=MM7Receiver\">");
        writer.println("</head>");

        BufferedReader reader = null;
        try{
            reader = new BufferedReader(new FileReader("recv.txt"));

        }catch(FileNotFoundException fnfe) {
            writer.println("No message to view");
            writer.println("</body></html>");
            writer.flush();
            writer.close();
            return;
        }
        
        writer.println("<body>");
        String line = null;
        while ((line = reader.readLine()) != null) {
            writer.println("<p>" + line);
        }
        
        writer.println("</body></html>");
        writer.flush();
        writer.close();
    }

    public Response processDeliverRequest(DeliverRequest deliverRequest) throws MessageProcessingException {
        try {
            MimeMultipart content = (MimeMultipart) deliverRequest.getRawContent().getContent();
            MM7Process process = new MM7Process();

            String celular = deliverRequest.getSender();
            process.setNroCelular(celular);
            
            String subject = deliverRequest.getSubject();
            process.setSubject(subject);
            
            String strApp = deliverRequest.getRecipient();
            process.setApplication(strApp);
            
            process.processMultipart(content);
            
            //#####################################################
            //	Chequear si corresponden Ejecutar URLS de billing
            //#####################################################
            Properties propsCobro = new Properties();
            propsCobro.load(MM7Receiver.class.getResourceAsStream("/resources/billing.properties"));
            if (!propsCobro.isEmpty()) {
	        	ArrayList arrFiltros = loadBillingFilters(propsCobro);
	        	
	        	applyBillingGETs(arrFiltros, strApp, subject, celular, process.getIdref());
            }
            //###################################################
            //###################################################
        	
        	
            //###################################################
            //	Chequear si corresponden notificaciones SMS
            //###################################################
            Properties props = new Properties();
        	props.load(MM7Receiver.class.getResourceAsStream("/resources/sms-notification.properties"));
        	String strURL = props.getProperty("URL");
            
        	ArrayList arrFiltros = loadSMSNotificationFilters(props);
        	
        	applySMSNotifications(arrFiltros, strApp, subject, strURL, celular, process.getIdref());
            //###################################################
            //###################################################

/*
            if (strApp.equals("3000") || strApp.equals("3001")) {
            	String message = "Tu%20foto%20ha%20sido%20recibida.%20Ahora%20puedes%20regresar%20a%20tu%20mundo%20movistar.";
            	
            	if (subject != null) {
	            	if (subject.equalsIgnoreCase("album")) {
	            		message = "La%20foto%20que%20has%20enviado%20al%20album%20ha%20sido%20recibida.";
	            	} else if (subject.equalsIgnoreCase("fblog")) {
	            		message = "La%20foto%20que%20has%20enviado%20al%20FotoBlog%20ha%20sido%20recibida.";
	           		}
            	}
            	
            	SMSSender.sendSMS("http://10.0.0.243:8080/dsmpp/http-input/submit?message=" + message + "&sourceAddress=9946&recipients={@CELULAR@}&user=movistar_co&recipientsTon=2&recipientsNpi=1&sourceTon=1&sourceNpi=1&dataCodingScheme=ASCII", celular);
            	
            }else if (strApp.equals("3005") || strApp.equals("3006")) {
            	SMSSender.sendSMS("http://10.0.0.243:8080/dsmpp/http-input/submit?message=Tu%20mensaje%20fue%20recibido.%20Gracias&sourceAddress=9944&recipients={@CELULAR@}&user=movistar_co&recipientsTon=2&recipientsNpi=1&sourceTon=1&sourceNpi=1&dataCodingScheme=ASCII", celular);
            }
*/
        	
        }catch(java.io.IOException ie) {
        	System.out.println("IOException: " + ie.getMessage());
        }catch(MessagingException me) {
            System.out.println("MessagingException: " + me.getMessage());
        }catch(APIException e) {
			e.printStackTrace();
		}
        
        // create a response object and send it back to the relay
        DeliverResponse response = new DeliverResponse();
        response.setStatusCode(ErrorCode.SUCCESS);
        
        return response;
    }
    
    private ArrayList loadSMSNotificationFilters(Properties props) throws IOException {
    	ArrayList arrFiltros = new ArrayList();

    	int i = 1;
    	String strParam = props.getProperty("Filtro" + i);
    	while (strParam != null) {
    		StringTokenizer st = new StringTokenizer(strParam, "|");
    		if (st.countTokens() == 4) {
        		String sc = (String) st.nextToken();
        		String subject = (String) st.nextToken();
        		String message = (String) st.nextToken();
        		String sourceAddress = (String) st.nextToken();
    			
        		Filter obj = new Filter(sc, subject, message, sourceAddress);
        		arrFiltros.add(obj);
        	}
        	
    		strParam = props.getProperty("Filtro" + ++i);
    	}
        
    	return arrFiltros;
    }
    
    private void applySMSNotifications(ArrayList filters, String app, String subject, String strURL, String celular, long idRef) {
		if (celular != null) {
	    	Iterator it = filters.iterator();
	    	
	    	while (it.hasNext()) {
	    		Filter f = (Filter) it.next();
	
	    		if (app.equalsIgnoreCase(f.getSc())) {
	
	    				String filterSubject = f.getSubject();
	    				boolean ok = false;
	    				
	    				if (filterSubject == null && subject == null) {
	    					ok = true;
	    					
	    				} else if (filterSubject == null) {
	    					ok = true;
	    					
	    				} else if (subject != null && filterSubject != null) { 
	    					if (subject.equalsIgnoreCase(filterSubject))
		    					ok = true;
	    				}
	    				
	    				if (ok) {
	    					if (celular.indexOf("57") == 0) {
	    						celular = celular.substring(2, celular.length());
	    					}
	    					
	    					strURL = strURL.replaceAll("\\{@CELULAR@\\}", celular);
	    					strURL = strURL.replaceAll("\\{@MESSAGE@\\}", f.getMessage());
	    					strURL = strURL.replaceAll("\\{@ADDRESS@\\}", f.getSourceAddress());
	    					strURL = strURL.replaceAll("\\{@APPLICATION@\\}", app);
	    					strURL = strURL.replaceAll("\\{@REFERENCE_ID@\\}", String.valueOf(idRef));
	    					
	    			    	System.out.println("strURL=" + strURL);
		    				
		                	try {
								System.out.println(">>>>>>>>>>>>>>>> ANTES <<<<<<<<<<<<<<<<<<<<<<<<<<");
								SMSSender.sendSMS(strURL);
								System.out.println(">>>>>>>>>>>>>>>> DESPUES <<<<<<<<<<<<<<<<<<<<<<<<<<");
								
							} catch (IOException e) {
								e.printStackTrace();
							}
		    			}
	
	    		}
	    	}
		}
    }

    private ArrayList loadBillingFilters(Properties props) throws IOException {
    	ArrayList arrFiltros = new ArrayList();

    	int i = 1;
    	String strParam = props.getProperty("Filtro" + i);
    	while (strParam != null) {
    		StringTokenizer st = new StringTokenizer(strParam, "|");
    		if (st.countTokens() == 4) {
        		String sc = (String) st.nextToken();
        		String subject = (String) st.nextToken();
        		String url = (String) st.nextToken();
        		String sourceAddress = (String) st.nextToken();
    			
        		FilterBilling obj = new FilterBilling(sc, subject, url, sourceAddress);
        		arrFiltros.add(obj);
        	}
        	
    		strParam = props.getProperty("Filtro" + ++i);
    	}
        
    	return arrFiltros;
    }
    
    private void applyBillingGETs(ArrayList filters, String app, String subject, String celular, long idRef) {
    	Iterator it = filters.iterator();
    	
    	while (it.hasNext()) {
    		FilterBilling f = (FilterBilling) it.next();

    		if (app.equalsIgnoreCase(f.getSc())) {

    				String filterSubject = f.getSubject();
    				boolean ok = false;
    				
    				if (filterSubject == null && subject == null) {
    					ok = true;
    					
    				} else if (filterSubject == null) {
    					ok = true;
    					
    				} else if (subject != null && filterSubject != null) { 
    					if (subject.equalsIgnoreCase(filterSubject))
	    					ok = true;
    				}
    				
    				if (ok) {
    					String strURL = "";

    					strURL = f.getURLBilling();
    					
    					strURL = strURL.replaceAll("\\{@ADDRESS@\\}", f.getSourceAddress());
    					strURL = strURL.replaceAll("\\{@APPLICATION@\\}", app);
    					strURL = strURL.replaceAll("\\{@REFERENCE_ID@\\}", String.valueOf(idRef));
    					strURL = strURL.replaceAll("\\{@CELULAR@\\}", celular);
	    				
    			    	System.out.println("BillingURL=" + strURL);
    			    	
	                	try {
							System.out.println(">>>>>>>>>>>>>>>> ANTES <<<<<<<<<<<<<<<<<<<<<<<<<<");
							HTTPGetter.get(strURL);
							System.out.println(">>>>>>>>>>>>>>>> DESPUES <<<<<<<<<<<<<<<<<<<<<<<<<<");
							
						} catch (IOException e) {
							e.printStackTrace();
						}
	    			}

    		}
    	}
    	
    }
    
    public Response processDeliveryReport(DeliveryReport deliveryReport) throws MessageProcessingException {
        // create a response object and send it back to the Openwave MMSC relay
        DeliveryReportResponse response = new DeliveryReportResponse();
        response.setStatusCode(ErrorCode.SUCCESS);
        response.setStatusText( "got it!" );
        
        return response;
    }

    public Response processReadReply( ReadReply readReply ) throws MessageProcessingException {
        return null;
    }

}