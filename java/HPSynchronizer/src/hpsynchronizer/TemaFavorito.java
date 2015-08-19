/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package hpsynchronizer;

import java.io.Serializable;
import javax.persistence.Basic;
import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.Id;
import javax.persistence.NamedQueries;
import javax.persistence.NamedQuery;
import javax.persistence.Table;

/**
 *
 * @author leon
 */
@Entity
@Table(name = "favoritos_temas")
@NamedQueries({
    @NamedQuery(name = "TemaFavorito.findAll", query = "SELECT t FROM TemaFavorito t"),
    @NamedQuery(name = "TemaFavorito.findByIdportal", query = "SELECT t FROM TemaFavorito t WHERE t.idportal = :idportal"),
    @NamedQuery(name = "TemaFavorito.findByIdtema", query = "SELECT t FROM TemaFavorito t WHERE t.idtema = :idtema"),
    @NamedQuery(name = "TemaFavorito.findByOrden", query = "SELECT t FROM TemaFavorito t WHERE t.orden = :orden"),
    @NamedQuery(name = "TemaFavorito.findByTrackTitle", query = "SELECT t FROM TemaFavorito t WHERE t.trackTitle = :trackTitle"),
    @NamedQuery(name = "TemaFavorito.findByIdalbum", query = "SELECT t FROM TemaFavorito t WHERE t.idalbum = :idalbum"),
    @NamedQuery(name = "TemaFavorito.findByIdartista", query = "SELECT t FROM TemaFavorito t WHERE t.idartista = :idartista"),
    @NamedQuery(name = "TemaFavorito.findByNombreArtista", query = "SELECT t FROM TemaFavorito t WHERE t.nombreArtista = :nombreArtista"),
    @NamedQuery(name = "TemaFavorito.findByOrderId", query = "SELECT t FROM TemaFavorito t WHERE t.orderId = :orderId"),
    @NamedQuery(name = "TemaFavorito.findByBundleOrderId", query = "SELECT t FROM TemaFavorito t WHERE t.bundleOrderId = :bundleOrderId"),
    @NamedQuery(name = "TemaFavorito.findByAlbum", query = "SELECT t FROM TemaFavorito t WHERE t.album = :album"),
    @NamedQuery(name = "TemaFavorito.findByGenero", query = "SELECT t FROM TemaFavorito t WHERE t.genero = :genero")})
public class TemaFavorito implements Serializable {
    private static final long serialVersionUID = 1L;
    @Basic(optional = false)
    @Column(name = "idportal")
    private int idportal;
    @Basic(optional = false)
    @Column(name = "idtema")
    private int idtema;
    @Basic(optional = false)
    @Column(name = "orden")
    private int orden;
    @Basic(optional = false)
    @Column(name = "track_title")
    private String trackTitle;
    @Basic(optional = false)
    @Column(name = "idalbum")
    private int idalbum;
    @Basic(optional = false)
    @Column(name = "idartista")
    private String idartista;
    @Basic(optional = false)
    @Column(name = "nombre_artista")
    private String nombreArtista;
    @Id
    @Basic(optional = false)
    @Column(name = "order_id")
    private String orderId;
    @Basic(optional = false)
    @Column(name = "bundle_order_id")
    private String bundleOrderId;
    @Basic(optional = false)
    @Column(name = "album")
    private String album;
    @Basic(optional = false)
    @Column(name = "genero")
    private String genero;

    public TemaFavorito() {
    }

    public TemaFavorito(String orderId) {
        this.orderId = orderId;
    }

    public TemaFavorito(String orderId, int idportal, int idtema, int orden, String trackTitle, int idalbum, String idartista, String nombreArtista, String bundleOrderId, String album, String genero) {
        this.orderId = orderId;
        this.idportal = idportal;
        this.idtema = idtema;
        this.orden = orden;
        this.trackTitle = trackTitle;
        this.idalbum = idalbum;
        this.idartista = idartista;
        this.nombreArtista = nombreArtista;
        this.bundleOrderId = bundleOrderId;
        this.album = album;
        this.genero = genero;
    }

    public int getIdportal() {
        return idportal;
    }

    public void setIdportal(int idportal) {
        this.idportal = idportal;
    }

    public int getIdtema() {
        return idtema;
    }

    public void setIdtema(int idtema) {
        this.idtema = idtema;
    }

    public int getOrden() {
        return orden;
    }

    public void setOrden(int orden) {
        this.orden = orden;
    }

    public String getTrackTitle() {
        return trackTitle;
    }

    public void setTrackTitle(String trackTitle) {
        this.trackTitle = trackTitle;
    }

    public int getIdalbum() {
        return idalbum;
    }

    public void setIdalbum(int idalbum) {
        this.idalbum = idalbum;
    }

    public String getIdartista() {
        return idartista;
    }

    public void setIdartista(String idartista) {
        this.idartista = idartista;
    }

    public String getNombreArtista() {
        return nombreArtista;
    }

    public void setNombreArtista(String nombreArtista) {
        this.nombreArtista = nombreArtista;
    }

    public String getOrderId() {
        return orderId;
    }

    public void setOrderId(String orderId) {
        this.orderId = orderId;
    }

    public String getBundleOrderId() {
        return bundleOrderId;
    }

    public void setBundleOrderId(String bundleOrderId) {
        this.bundleOrderId = bundleOrderId;
    }

    public String getAlbum() {
        return album;
    }

    public void setAlbum(String album) {
        this.album = album;
    }

    public String getGenero() {
        return genero;
    }

    public void setGenero(String genero) {
        this.genero = genero;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (orderId != null ? orderId.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof TemaFavorito)) {
            return false;
        }
        TemaFavorito other = (TemaFavorito) object;
        if ((this.orderId == null && other.orderId != null) || (this.orderId != null && !this.orderId.equals(other.orderId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "hpsynchronizer.TemaFavorito[orderId=" + orderId + "]";
    }

}
