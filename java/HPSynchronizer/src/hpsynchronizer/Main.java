/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package hpsynchronizer;

import java.util.ArrayList;
import java.util.List;
import javax.persistence.EntityManager;
import javax.persistence.EntityManagerFactory;
import javax.persistence.EntityTransaction;
import javax.persistence.Persistence;
import org.apache.log4j.Logger;

/**
 *
 * @author leon
 */
public class Main {

    
    static Logger logger = Logger.getLogger(Main.class);
    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
        MainProcess mp = new MainProcess();
        try {
            mp.Start();
        } catch (Exception ex) {
            logger.fatal("ERROR: Error no reconocido en el main process: " + ex.getMessage());
        }

        /*
        EntityManagerFactory emf = Persistence.createEntityManagerFactory("HPSynchronizerPU");
        EntityManager em = emf.createEntityManager();
        try {
            WSMaxxClient cliente = WSMaxxClient.getInstancia();
            EntityTransaction et = em.getTransaction();
            et.begin();
            List<Tema> temas = cliente.getTemas(15680, 1, 11);
            for(Tema t: temas) {
              //  em.persist(t);

                TemaErroneo fail = new TemaErroneo(t, new Exception("Todo mal"));
                em.persist(fail);
            }

            List<Genero> generos = cliente.getGeneros();
            for(Genero g: generos) {
                em.persist(g);
            }

            et.commit();
        } catch (WSUnavailableException ex) {
            Logger.getLogger(Main.class.getName()).log(Level.SEVERE, null, ex);
        } catch(Exception ex) {
            System.out.println("Error insertando: " + ex.getMessage());
         }*/
    }

}
