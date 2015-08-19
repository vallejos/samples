package hpsynchronizer;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import javax.persistence.EntityManager;
import javax.persistence.EntityManagerFactory;
import javax.persistence.NoResultException;
import javax.persistence.Persistence;
import javax.persistence.Query;
import org.apache.log4j.Logger;

/**
 *
 * @author leon
 */
public class AlbumController {

    private static AlbumController instance = null;
    private Map<String, Album> albums = null;
    private Map<String, Artista> artistas = null;
    private EntityManager eManager = null;

    static Logger logger = Logger.getLogger(TemaController.class);

    public static AlbumController getInstance() {
        if (instance == null) {
            instance = new AlbumController();
        }
        return instance;
    }

    private AlbumController() {
        this.albums = new HashMap<String, Album>();
        this.artistas = new HashMap<String, Artista>();
        EntityManagerFactory emf = Persistence.createEntityManagerFactory("HPSynchronizerPU");
        this.eManager = emf.createEntityManager();
    }

    /**
     * Agrega un tema al album especificado por su upc
     * En caso en que el album no exista, se intentan obtener los datos del mismo
     * del WS. Si se obtienen, se crea el Album y la asociación, en caso contrario
     * falla.
     * @param t Tema a ser agregado
     * @param upc Idenficador único del album
     * @throws NonExistentAlbumException en caso en que no se pueda obtener los datos
     * del album del WS
     */
    public void addTemaToAlbum(Tema tema, Genero genero) 
            throws NonExistentAlbumException, WSUnavailableException, ArtistaVacioException {

        // Obtenemos el album de la memoria, de la base, o lo creamos.
        Album album = null;
        String upc = tema.getUpc();
        if (!this.albums.containsKey(upc)) {
            Query q = this.eManager.createNamedQuery("Album.findByUpc");
            q.setParameter("upc", upc);
            try {
                album = (Album) q.getSingleResult();
            } catch (NoResultException exc) {
                album = WSMaxxClient.getInstancia().getAlbum(upc);
                album.addGenero(genero);
            } finally {
                if (album != null) {
                    this.albums.put(upc, album);
                }
            }
        } else {
            album = this.albums.get(upc);
        }

        // Se ha obtenido o creado el album, se le agrega el tema
        album.addTema(tema);
    }

    /**
     * Obtiene todos los albums creados al momento
     * @return List con objetos <em>Album</em>
     */
    public List<Album> getAllAlbums() {
        return new ArrayList<Album>(this.albums.values());
    }

    /**
     * Busca o crea un Artista, agregándolo a la colección general si corresponde.
     * @param nombre Nombre para buscar o ingresar el artista
     * @return Objeto de tipo Artista identificado
     */
    public Artista getArtista(String nombre) throws ArtistaVacioException {
        Artista artista = null;
        if (!this.artistas.containsKey(nombre)) {
            Query q = this.eManager.createNamedQuery("Artista.findByNombre");
            q.setParameter("nombre", nombre);
            try {
                artista = (Artista) q.getSingleResult();
            } catch (NoResultException exc) {
                artista = new Artista(nombre);
            } finally {
                this.artistas.put(nombre, artista);
            }
        } else {
            artista = this.artistas.get(nombre);
        }
        logger.debug("Creando Artista: " + artista.getNombre());
        return artista;
    }

    /**
     * Borra las referencias a la lista de albumscon objetivo de liberar la memoria
     */
    public void reset() {
        this.albums = new HashMap<String, Album>();
    }
}
