

package hpsynchronizer;

/**
 *
 * @author leon
 */
class NonExistentAlbumException extends Exception {

    public NonExistentAlbumException(){
        super("No se ha encontrado el album");

    }

}
