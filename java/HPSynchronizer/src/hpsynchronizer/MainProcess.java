/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package hpsynchronizer;

import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.util.List;
import java.util.Properties;
import java.util.logging.Level;
import javax.persistence.EntityManager;
import javax.persistence.EntityManagerFactory;
import javax.persistence.EntityTransaction;
import javax.persistence.Persistence;
import org.apache.log4j.Logger;

/**
 *
 * @author fernando
 */
public class MainProcess {
    private int initGeneroId = 0;
    private int initPage = 0;
    static final int MAX_RETRIES = 10;
    static final String CONF_FILE = "config.properties";



    static Logger logger = Logger.getLogger(MainProcess.class);
    
    public void Start() {
        logger.info("Inicianlizando proceso....");
        this.loadConfig();
        int currentTry = 0;
        int currentPage = this.initPage;
        Genero generoError = null;
        String lastTryMsg = "Persistencia de emergencia: OK";
        boolean allGood = true;

        do {
            try {
                allGood = true;
                WSMaxxClient cliente = WSMaxxClient.getInstancia();
                TemaController tc = TemaController.getInstance();
                AlbumController ac = AlbumController.getInstance();
                List<Genero> generos = cliente.getGeneros();
                for(int i = 0; i < generos.size(); i++) {

                    Genero g = generos.get(i);
                    if (g.getIdgrupo() != 15680) {
                        tc.getAllTemas(g, currentPage);
                        g.setAlbums(ac.getAllAlbums());
                        this.persistirGenero(g);
                        ac.reset();
                    }
                    generos.remove(g);
                    break; // TODO QUITAR O SE CAGA TOD Y TODO MORIMOS

                }
            } catch (WSUnavailableException ex) {
                this.initGeneroId = ex.getGenero().getId();
                generoError = ex.getGenero();
                this.initPage = ex.getPagina();
                currentTry++;
                allGood = false;
            } catch (UnexpectedErrorException ex) {
                this.initGeneroId = ex.getGenero().getId();
                this.initPage = ex.getPagina();
                generoError = ex.getGenero();
                currentTry++;
                allGood = false;
            }
        } while (currentTry < MAX_RETRIES && !allGood);

        //Si terminó de intentar y sigue con error
        if(currentTry == MAX_RETRIES) {
            this.saveCurrentConfig();

            try {
                this.persistirGenero(generoError);
            } catch (UnexpectedErrorException ex) {
                //Intenta persistir lo que hay, pero igual falla
                lastTryMsg = "Persistencia de emergencia: FAIL :: " + ex.getMessage();
            }
            logger.info(lastTryMsg);

            logger.error("Falló la carga de datos de HP más de " + MAX_RETRIES + " veces, ver log por más detalles");
        } else {
            logger.error("Proceso terminado satisfactoriamente");
        }
    }

    private void persistirGenero(Genero g) throws UnexpectedErrorException{
        logger.info("Persistiendo genero: " + g.getNombre());
        try {
            EntityManagerFactory emf = Persistence.createEntityManagerFactory("HPSynchronizerPU");
            EntityManager em = emf.createEntityManager();
            EntityTransaction et = em.getTransaction();
            et.begin();
            em.persist(g);
            et.commit();
        } catch (Exception ex) {
            logger.error("Error ineperado: " + ex.getMessage());
            throw new UnexpectedErrorException(ex.getMessage());
        }
    }

    private void loadConfig() {
        InputStream is = null;
        try {
            logger.info("Cargando configuración....");
            Properties prop = new Properties();
            is = new FileInputStream(CONF_FILE);
            prop.load(is);
            try {
                this.initGeneroId = (Integer) prop.get("initGeneroId");
                this.initPage = (Integer) prop.get("initPage");
             } catch (NullPointerException ex) {
                this.initGeneroId = 0;
                this.initPage = 0;
             }
            logger.info("initGeneroId: " + this.initGeneroId);
            logger.info("initPage:" + this.initPage);
        }  catch (FileNotFoundException ex) {
            java.util.logging.Logger.getLogger(MainProcess.class.getName()).log(Level.SEVERE, null, ex);
        } catch (IOException ex) {
            java.util.logging.Logger.getLogger(MainProcess.class.getName()).log(Level.SEVERE, null, ex);
        } finally {
            try {
                is.close();
            } catch (IOException ex) {
                java.util.logging.Logger.getLogger(MainProcess.class.getName()).log(Level.SEVERE, null, ex);
            }
        }
    }

    private void saveCurrentConfig() {
        OutputStream out = null;
        try {
            logger.info("Salvando configuración....");
            logger.info("InitGeneroID:" + this.initGeneroId);
            logger.info("InitPasge:" + this.initPage);
            Properties prop = new Properties();
            prop.put("initGeneroId", this.initGeneroId);
            prop.put("initPage", this.initPage);
            out = new FileOutputStream(CONF_FILE);
            prop.store(out, null);
        } catch (FileNotFoundException ex) {
            java.util.logging.Logger.getLogger(MainProcess.class.getName()).log(Level.SEVERE, null, ex);
        } catch (IOException ex) {
            java.util.logging.Logger.getLogger(MainProcess.class.getName()).log(Level.SEVERE, null, ex);
        } finally {
            try {
                out.close();
            } catch (IOException ex) {
                java.util.logging.Logger.getLogger(MainProcess.class.getName()).log(Level.SEVERE, null, ex);
            }
        }


    }

}
