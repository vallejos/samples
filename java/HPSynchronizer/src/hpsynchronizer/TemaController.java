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
public class TemaController {

    private final static int CANT_PER_PAGE = 500;
    private static TemaController instance = null;
    private List<Tema> temas = null;
    private EntityManager eManager = null;

    static Logger logger = Logger.getLogger(TemaController.class);

    public static TemaController getInstance() {
        if (instance == null) {
            instance = new TemaController();
        }
        return instance;
    }

    private TemaController() {
        this.temas = new ArrayList<Tema>();
        EntityManagerFactory emf = Persistence.createEntityManagerFactory("HPSynchronizerPU");
        this.eManager = emf.createEntityManager();
    }

    public void getAllTemas(Genero genero, int page) throws WSUnavailableException, UnexpectedErrorException {
        List<Tema> tempTemas = new ArrayList<Tema>(TemaController.CANT_PER_PAGE);
        logger.debug("Invocando WS para obtener temas");
        tempTemas = WSMaxxClient.getInstancia().getTemas(genero.getIdgrupo(), page, TemaController.CANT_PER_PAGE);
        logger.debug("Respuesta del WS exitosa");
        logger.debug("Temp temas: " + tempTemas.toString());
        while (!tempTemas.isEmpty()) { // Iteracion por pagina
            logger.debug("Iterando temas");
            // Iteramos todos los temas de la pagina obtenida
            for (Tema tema : tempTemas) {
                try {
                    AlbumController.getInstance().addTemaToAlbum(tema, genero);
                } catch (ArtistaVacioException exc) {
                    TemaErroneo temaError = new TemaErroneo(tema, exc);
                    EntityTransaction et = this.eManager.getTransaction();
                    et.begin();
                    logger.info("Persistiendo un FAILTRACK!!!!" + temaError.getTrackTitle());
                    this.eManager.persist(temaError);
                    et.commit();
                } catch (NonExistentAlbumException exc) {
                    TemaErroneo temaError = new TemaErroneo(tema, exc);
                    EntityTransaction et = this.eManager.getTransaction();
                    et.begin();
                    logger.info("Persistiendo un FAILTRACK!!!!" + temaError.getTrackTitle());
                    this.eManager.persist(temaError);
                    et.commit();
                } catch (WSUnavailableException exc){
                    exc.setPagina(page);
                    exc.setGenero(genero);
                    throw exc;
                } catch (Exception exc){
                    UnexpectedErrorException ue = new UnexpectedErrorException(exc.getMessage());
                    ue.setGenero(genero);
                    ue.setPagina(page);
                    throw ue;
                }
            }

            
            // Cleanup y seteos proxima iteracion
            this.temas.addAll(tempTemas);
            page++;
           //break; // TODO: QUITAR ESTO O EL FIN DEL MUNDO LLEGARA
            tempTemas = WSMaxxClient.getInstancia().getTemas(genero.getIdgrupo(), page, TemaController.CANT_PER_PAGE);
        }
    }
}
