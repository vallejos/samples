/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package hpsynchronizer;

import java.io.Serializable;
import java.util.Date;
import java.util.List;
import javax.persistence.Basic;
import javax.persistence.CascadeType;
import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.GeneratedValue;
import javax.persistence.GenerationType;
import javax.persistence.Id;
import javax.persistence.JoinColumn;
import javax.persistence.ManyToMany;
import javax.persistence.NamedQueries;
import javax.persistence.NamedQuery;
import javax.persistence.Table;
import javax.persistence.Temporal;
import javax.persistence.TemporalType;
import javax.persistence.Transient;

/**
 *
 * @author leon
 */
@Entity
@Table(name = "temas")
@NamedQueries({
    @NamedQuery(name = "Tema.findAll", query = "SELECT t FROM Tema t"),
    @NamedQuery(name = "Tema.findById", query = "SELECT t FROM Tema t WHERE t.id = :id"),
    @NamedQuery(name = "Tema.findByIsrc", query = "SELECT t FROM Tema t WHERE t.isrc = :isrc"),
    @NamedQuery(name = "Tema.findByTrackNumber", query = "SELECT t FROM Tema t WHERE t.trackNumber = :trackNumber"),
    @NamedQuery(name = "Tema.findByTrackTitle", query = "SELECT t FROM Tema t WHERE t.trackTitle = :trackTitle"),
    @NamedQuery(name = "Tema.findByTrackVersionTitle", query = "SELECT t FROM Tema t WHERE t.trackVersionTitle = :trackVersionTitle"),
    @NamedQuery(name = "Tema.findByTrackLength", query = "SELECT t FROM Tema t WHERE t.trackLength = :trackLength"),
    @NamedQuery(name = "Tema.findByActivo", query = "SELECT t FROM Tema t WHERE t.activo = :activo"),
    @NamedQuery(name = "Tema.findByVolumen", query = "SELECT t FROM Tema t WHERE t.volumen = :volumen"),
    @NamedQuery(name = "Tema.findByValor", query = "SELECT t FROM Tema t WHERE t.valor = :valor"),
    @NamedQuery(name = "Tema.findByIdsello", query = "SELECT t FROM Tema t WHERE t.idsello = :idsello"),
    @NamedQuery(name = "Tema.findByOrderId", query = "SELECT t FROM Tema t WHERE t.orderId = :orderId"),
    @NamedQuery(name = "Tema.findByBundleOrderId", query = "SELECT t FROM Tema t WHERE t.bundleOrderId = :bundleOrderId"),
    @NamedQuery(name = "Tema.findByLicenseProviderId", query = "SELECT t FROM Tema t WHERE t.licenseProviderId = :licenseProviderId"),
    @NamedQuery(name = "Tema.findByTariffClass", query = "SELECT t FROM Tema t WHERE t.tariffClass = :tariffClass"),
    @NamedQuery(name = "Tema.findBySellOnlyOnBundle", query = "SELECT t FROM Tema t WHERE t.sellOnlyOnBundle = :sellOnlyOnBundle")})
public class Tema implements Serializable {
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    @Column(name = "id")
    private Integer id;
    @Basic(optional = false)
    @Column(name = "isrc")
    private String isrc;
    @Basic(optional = false)
    @Column(name = "track_number")
    private short trackNumber;
    @Basic(optional = false)
    @Column(name = "track_title")
    private String trackTitle;
    @Column(name = "track_version_title")
    private String trackVersionTitle;
    @Column(name = "track_length")
    @Temporal(TemporalType.TIME)
    private Date trackLength;
    @Column(name = "activo")
    private Boolean activo;
    @Column(name = "volumen")
    private Short volumen;
    @Column(name = "valor")
    private Short valor;
    @Column(name = "idsello")
    private Integer idsello;
    @Basic(optional = false)
    @Column(name = "order_id")
    private String orderId;
    @Column(name = "bundle_order_id")
    private String bundleOrderId;
    @Column(name = "license_provider_id")
    private Integer licenseProviderId;
    @Column(name = "tariff_class")
    private Integer tariffClass;
    @Column(name = "sell_only_on_bundle")
    private Boolean sellOnlyOnBundle;
    @ManyToMany(mappedBy="temas",cascade = CascadeType.ALL)
    @JoinColumn(name="idalbum")
    private List<Album> albums;
    @Transient
    private String upc;


    public Tema() {
    }

    public Tema(Integer id) {
        this.id = id;
    }

    public Tema(Integer id, String isrc, short trackNumber, String trackTitle, String orderId) {
        this.id = id;
        this.isrc = isrc;
        this.trackNumber = trackNumber;
        this.trackTitle = trackTitle;
        this.orderId = orderId;
    }

    public List<Album> getAlbums() {
        return albums;
    }

    public void setAlbums(List<Album> albums) {
        this.albums = albums;
    }

    public String getUpc() {
        return upc;
    }

    public void setUpc(String upc) {
        this.upc = upc;
    }
    

    public Integer getId() {
        return id;
    }

    public void setId(Integer id) {
        this.id = id;
    }

    public String getIsrc() {
        return isrc;
    }

    public void setIsrc(String isrc) {
        this.isrc = isrc;
    }

    public short getTrackNumber() {
        return trackNumber;
    }

    public void setTrackNumber(short trackNumber) {
        this.trackNumber = trackNumber;
    }

    public String getTrackTitle() {
        return trackTitle;
    }

    public void setTrackTitle(String trackTitle) {
        this.trackTitle = trackTitle;
    }

    public String getTrackVersionTitle() {
        return trackVersionTitle;
    }

    public void setTrackVersionTitle(String trackVersionTitle) {
        this.trackVersionTitle = trackVersionTitle;
    }

    public Date getTrackLength() {
        return trackLength;
    }

    public void setTrackLength(Date trackLength) {
        this.trackLength = trackLength;
    }

    public Boolean getActivo() {
        return activo;
    }

    public void setActivo(Boolean activo) {
        this.activo = activo;
    }

    public Short getVolumen() {
        return volumen;
    }

    public void setVolumen(Short volumen) {
        this.volumen = volumen;
    }

    public Short getValor() {
        return valor;
    }

    public void setValor(Short valor) {
        this.valor = valor;
    }

    public Integer getIdsello() {
        return idsello;
    }

    public void setIdsello(Integer idsello) {
        this.idsello = idsello;
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

    public Integer getLicenseProviderId() {
        return licenseProviderId;
    }

    public void setLicenseProviderId(Integer licenseProviderId) {
        this.licenseProviderId = licenseProviderId;
    }

    public Integer getTariffClass() {
        return tariffClass;
    }

    public void setTariffClass(Integer tariffClass) {
        this.tariffClass = tariffClass;
    }

    public Boolean getSellOnlyOnBundle() {
        return sellOnlyOnBundle;
    }

    public void setSellOnlyOnBundle(Boolean sellOnlyOnBundle) {
        this.sellOnlyOnBundle = sellOnlyOnBundle;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (id != null ? id.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof Tema)) {
            return false;
        }
        Tema other = (Tema) object;
        if ((this.id == null && other.id != null) || (this.id != null && !this.id.equals(other.id))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "hpsynchronizer.Tema[id=" + id + "]";
    }

}
