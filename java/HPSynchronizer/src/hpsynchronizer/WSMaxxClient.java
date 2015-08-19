/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package hpsynchronizer;

import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.Date;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import org.apache.log4j.Logger;
import org.jdom.Document;
import org.jdom.Element;
import org.jdom.JDOMException;
import org.jdom.input.SAXBuilder;


/**
 *
 * @author fernando
 */
public class WSMaxxClient {
    static final private String URL = "http://maxx.me.net-m.net/me/maxx/<contractId>/";
    static final private int contractId = 2146850;
    static final private int MAXRETRIES = 3;

    static Logger logger = Logger.getLogger(WSMaxxClient.class);

    
    static  private WSMaxxClient instancia = null;

    static public WSMaxxClient getInstancia() {
        if(instancia == null) {
            instancia=  new WSMaxxClient();
        }
        return instancia;
    }


    public List<Genero> getGeneros() throws WSUnavailableException{

        String methodName = "contentGroups";
        HashMap<String, Object> params = new HashMap<String, Object>();
        params.put("contentTypeKey", "FULLTRACK");

        Document docRes = this.makeCall(methodName, params);
        Element rootElem = docRes.getRootElement();
        Iterator iGeneros = rootElem.getChildren("contentGroup").iterator();

        ArrayList<Genero> lGeneros = new ArrayList<Genero>();
        while(iGeneros.hasNext()) {
            Element curr = (Element)iGeneros.next();
            Genero dummy = new Genero();
            dummy.setActivo(true);
            dummy.setIdgrupo(Integer.parseInt(curr.getAttributeValue("id")));
            dummy.setNombre(curr.getAttributeValue("name"));

            lGeneros.add(dummy);
        }

        return lGeneros;
    }


    /**
     * Retorna UN album, a partir de su UPC
     * @param upc Identificador único del álbum
     * @return
     * @throws WSUnavailableException  Si la conexión con el WS no es posible
     * @throws NonExistentAlbumException Si el UPC no retorna resultados
     * @throws ArtistaVacioException    Si el Artista viene vacío
     */
    public Album getAlbum(String upc) throws WSUnavailableException, NonExistentAlbumException, ArtistaVacioException {

        String methodName = "items";
        AlbumController aController = AlbumController.getInstance();
        HashMap<String, Object> params = new HashMap<String, Object>();
        params.put("contentTypeKey", "FT_BUNDLE");
        params.put("icpn", upc);
        Document resDoc = this.makeCall(methodName, params);

        Element items = resDoc.getRootElement();
        if (items != null) {
            Element album = items.getChild("item");
            if (album != null) {
                Album oAlbum = new Album();
                oAlbum.setBundleId(album.getAttributeValue("orderId"));
                oAlbum.setPrdTitle(album.getAttributeValue("title"));

                Artista artist = aController.getArtista(album.getAttributeValue("artist"));

                oAlbum.addArtista(artist);
                oAlbum.setUpc(upc);
                return oAlbum;
            } else {
                throw new NonExistentAlbumException();
            }
        } else {
            throw new NonExistentAlbumException();
        }

    }

    /**
     * Retorna la lista de temas para la pagina solicitada
     * @param groupId  El ID del contentGroup (o genero)
     * @param page La pagina solicitada
     * @param cantPorPage  Cantidad de temas  a traer por página, máximo 500
     * @return List<Tema>
     * @throws WSUnavailableException
     */
    public List<Tema> getTemas(int groupId, int page, int cantPorPage) throws WSUnavailableException {
        String methodName = "items";
        HashMap<String, Object> params = new HashMap<String, Object>();
        int init = ((page -1 ) * (cantPorPage)) + 1;
        params.put("contentTypeKey", "FULLTRACK");
        params.put("maxSize", cantPorPage);
        params.put("start", init);
        params.put("contentGroupId", groupId);


        Document docRes = this.makeCall(methodName, params);

        List<Tema> temas = new ArrayList<Tema>();
        Element rootElem = docRes.getRootElement();
        Iterator iElements = rootElem.getChildren("item").iterator();
        while(iElements.hasNext()) {
            Element elem = (Element)iElements.next();
            Tema dummy = new Tema();
            dummy.setOrderId(elem.getAttributeValue("orderId"));
            dummy.setTrackTitle(elem.getAttributeValue("title"));
            dummy.setTariffClass(Integer.parseInt(elem.getAttributeValue("tariffClass")));
            dummy.setBundleOrderId(elem.getAttributeValue("bundleOrderId"));
            dummy.setIsrc(elem.getAttributeValue("isrc"));
            dummy.setTrackNumber((short) Integer.parseInt(elem.getAttributeValue("track")));

            int min = (int) Integer.parseInt(elem.getAttributeValue("length")) / 60;
            int secs = (int) Integer.parseInt(elem.getAttributeValue("length")) % 60;

            Date duracion = new Date(0, 0, 0, 0, min,secs);

            dummy.setTrackLength(duracion);
            dummy.setActivo(true);
            dummy.setIdsello(1);
            dummy.setVolumen((short) Integer.parseInt(elem.getAttributeValue("volume")));

            dummy.setUpc(elem.getAttributeValue("icpn"));
            
            dummy.setLicenseProviderId(Integer.parseInt(elem.getAttributeValue("licenseProviderId")));
            dummy.setSellOnlyOnBundle(Boolean.getBoolean(elem.getAttributeValue("sellOnlyInBundle")));
            temas.add(dummy);
        }


        return temas;
    }

    private Document makeCall(String methodName, HashMap<String, Object> parametros) throws WSUnavailableException{
        int retries = 0;
        Document docRes = null;
        boolean allGood = true;
        do {
            try {
                docRes = this.Call(methodName, parametros);
            } catch (Exception ex) {
                allGood = false;
                retries++;
                System.out.println("Error accediendo al WS: " + ex.getMessage());
            }

        } while (!allGood && retries < WSMaxxClient.MAXRETRIES);
        if(retries == WSMaxxClient.MAXRETRIES && !allGood ) {
            throw new WSUnavailableException();
        }
        return docRes;

    }

    private Document Call(String methodName, HashMap<String, Object> parametros) throws ErrorReadingXMLException, IOException {    {
            String sUrl = WSMaxxClient.URL.replace("<contractId>", String.valueOf(WSMaxxClient.contractId));
            sUrl += methodName;
            try {
           
                sUrl += "?";
                Iterator iKeys = parametros.keySet().iterator();
                while (iKeys.hasNext()) {
                    String key = (String) iKeys.next();
                    sUrl += key + "=" + String.valueOf(parametros.get(key))+"&";
                }
                sUrl = sUrl.substring(0, sUrl.length() - 1);
                System.out.println("Ejecutando URL: " + sUrl);
                URL wsUrl = new URL(sUrl);


                SAXBuilder builder = new SAXBuilder(false);

                Document xmlDoc = builder.build(wsUrl);
                return xmlDoc;
            } catch (JDOMException ex) {
                throw new ErrorReadingXMLException("Error - El XML no se puede parsear (" + ex.getMessage() + ")");
            } catch (MalformedURLException ex) {
                throw new ErrorReadingXMLException("Error - La URL no es válida: " + sUrl + "(" + ex.getMessage() + ")");
            } 
        }
    }


}
