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
@Table(name = "ultimos_temas")
@NamedQueries({
    @NamedQuery(name = "TemaLanzamiento.findAll", query = "SELECT t FROM TemaLanzamiento t"),
    @NamedQuery(name = "TemaLanzamiento.findByIdportal", query = "SELECT t FROM TemaLanzamiento t WHERE t.idportal = :idportal"),
    @NamedQuery(name = "TemaLanzamiento.findByIdtema", query = "SELECT t FROM TemaLanzamiento t WHERE t.idtema = :idtema"),
    @NamedQuery(name = "TemaLanzamiento.findByOrden", query = "SELECT t FROM TemaLanzamiento t WHERE t.orden = :orden"),
    @NamedQuery(name = "TemaLanzamiento.findByTrackTitle", query = "SELECT t FROM TemaLanzamiento t WHERE t.trackTitle = :trackTitle"),
    @NamedQuery(name = "TemaLanzamiento.findByIdalbum", query = "SELECT t FROM TemaLanzamiento t WHERE t.idalbum = :idalbum"),
    @NamedQuery(name = "TemaLanzamiento.findByIdartista", query = "SELECT t FROM TemaLanzamiento t WHERE t.idartista = :idartista"),
    @NamedQuery(name = "TemaLanzamiento.findByNombreArtista", query = "SELECT t FROM TemaLanzamiento t WHERE t.nombreArtista = :nombreArtista"),
    @NamedQuery(name = "TemaLanzamiento.findByBundleId", query = "SELECT t FROM TemaLanzamiento t WHERE t.bundleId = :bundleId"),
    @NamedQuery(name = "TemaLanzamiento.findByOrderId", query = "SELECT t FROM TemaLanzamiento t WHERE t.orderId = :orderId"),
    @NamedQuery(name = "TemaLanzamiento.findByActivo", query = "SELECT t FROM TemaLanzamiento t WHERE t.activo = :activo")})
public class TemaLanzamiento implements Serializable {
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
    @Basic(optional = false)
    @Column(name = "bundle_id")
    private String bundleId;
    @Id
    @Basic(optional = false)
    @Column(name = "order_id")
    private String orderId;
    @Basic(optional = false)
    @Column(name = "activo")
    private boolean activo;

    public TemaLanzamiento() {
    }

    public TemaLanzamiento(String orderId) {
        this.orderId = orderId;
    }

    public TemaLanzamiento(String orderId, int idportal, int idtema, int orden, String trackTitle, int idalbum, String idartista, String nombreArtista, String bundleId, boolean activo) {
        this.orderId = orderId;
        this.idportal = idportal;
        this.idtema = idtema;
        this.orden = orden;
        this.trackTitle = trackTitle;
        this.idalbum = idalbum;
        this.idartista = idartista;
        this.nombreArtista = nombreArtista;
        this.bundleId = bundleId;
        this.activo = activo;
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

    public String getBundleId() {
        return bundleId;
    }

    public void setBundleId(String bundleId) {
        this.bundleId = bundleId;
    }

    public String getOrderId() {
        return orderId;
    }

    public void setOrderId(String orderId) {
        this.orderId = orderId;
    }

    public boolean getActivo() {
        return activo;
    }

    public void setActivo(boolean activo) {
        this.activo = activo;
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
        if (!(object instanceof TemaLanzamiento)) {
            return false;
        }
        TemaLanzamiento other = (TemaLanzamiento) object;
        if ((this.orderId == null && other.orderId != null) || (this.orderId != null && !this.orderId.equals(other.orderId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "hpsynchronizer.TemaLanzamiento[orderId=" + orderId + "]";
    }

}
