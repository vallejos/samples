/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package hpsynchronizer;

/**
 *
 * @author leon
 */
class UnexpectedErrorException extends Exception {

    private int pagina;

    private Genero genero;

    public UnexpectedErrorException(String message) {
        super(message);
    }

    public Genero getGenero() {
        return genero;
    }

    public void setGenero(Genero genero) {
        this.genero = genero;
    }

    public int getPagina() {
        return pagina;
    }

    public void setPagina(int pagina) {
        this.pagina = pagina;
    }

}
