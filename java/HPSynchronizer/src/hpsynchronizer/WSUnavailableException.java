/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package hpsynchronizer;

/**
 *
 * @author fernando
 */
class WSUnavailableException extends Exception {
    private Genero generoActual;
    private int lineaActual;

    public WSUnavailableException() {
        this.generoActual = null;
        this.lineaActual = 0;
    }

    public WSUnavailableException(Genero g, int l) {
        this.generoActual = g;
        this.lineaActual = l;
    }

    public void setGenero(Genero g) {
        this.generoActual = g;
    }

    public void setPagina(int p) {
        this.lineaActual = p;
    }

    public int getPagina() {
        return this.lineaActual;
    }

    public Genero getGenero() {
        return this.generoActual;
    }

}
