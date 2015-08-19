/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package hpsynchronizer;

import java.io.Serializable;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import javax.persistence.Basic;
import javax.persistence.Column;
import javax.persistence.EmbeddedId;
import javax.persistence.Entity;
import javax.persistence.GeneratedValue;
import javax.persistence.GenerationType;
import javax.persistence.Id;
import javax.persistence.NamedQueries;
import javax.persistence.NamedQuery;
import javax.persistence.Table;
import javax.persistence.Temporal;
import javax.persistence.TemporalType;

/**
 *
 * @author leon
 */
@Entity
@Table(name = "fail_temas")
public class TemaErroneo implements Serializable {


    private Tema tema;
    private Exception exMotivo;

    private Date fecha;


    public TemaErroneo() {
        this.tema = new Tema();
    }

    public TemaErroneo(Tema t, Exception mot) {
        this.tema = t;
        this.exMotivo = mot;
        Calendar cal = Calendar.getInstance();
        this.fecha = cal.getTime();

    }
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Id
    public Integer getId() {
        return this.tema.getId();
    }

    public void setId(Integer id) {
        this.tema.setId(id);
    }

    @Column(name="isrc")
    public String getIsrc() {
        return this.tema.getIsrc();
    }

    public void setIsrc(String isrc) {
        this.tema.setIsrc(isrc);
    }

    @Column(name="track_number")
    public short getTrackNumber() {
        return this.tema.getTrackNumber();
    }

    public void setTrackNumber(short trackNumber) {
        this.tema.setTrackNumber(trackNumber);
    }

    @Column(name="track_title")
    public String getTrackTitle() {
        return this.tema.getTrackTitle();
    }

    public void setTrackTitle(String trackTitle) {
        this.tema.setTrackTitle(trackTitle);
    }

    @Column(name="track_version_title")
    public String getTrackVersionTitle() {
        return this.tema.getTrackVersionTitle();
    }


    public void setTrackVersionTitle(String trackVersionTitle) {
        this.tema.setTrackVersionTitle(trackVersionTitle);
    }

    @Column(name="track_length")
    @Temporal(javax.persistence.TemporalType.TIME)
    public Date getTrackLength() {
        return this.tema.getTrackLength();
    }

    public void setTrackLength(Date trackLength) {
        this.tema.setTrackLength(trackLength);
    }

    @Column(name="volumen")
    public Short getVolumen() {
        return this.tema.getVolumen();
    }

    public void setVolumen(Short volumen) {
        this.tema.setVolumen(volumen);
    }

    @Column(name="valor")
    public Short getValor() {
        return this.tema.getValor();
    }

    public void setValor(Short valor) {
        this.tema.setValor(valor);
    }

    @Column(name="idsello")
    public Integer getIdsello() {
        return this.tema.getIdsello();
    }

    public void setIdsello(Integer idsello) {
        this.tema.setIdsello(idsello);
    }

    @Column(name="order_id")
    public String getOrderId() {
        return this.tema.getOrderId();
    }

    public void setOrderId(String ord){
        this.tema.setOrderId(ord);
    }

    @Column(name="bundle_order_id")
    public String getBundleOrderId() {
        return this.tema.getBundleOrderId();
    }

    public void setBundleOrderId(String bundleOrderId) {
        this.tema.setBundleOrderId(bundleOrderId);
    }

    @Column(name="license_provider_id")
    public Integer getLicenseProviderId() {
        return this.tema.getLicenseProviderId();
    }

    public void setLicenseProviderId(Integer licenseProviderId) {
        this.tema.setLicenseProviderId(licenseProviderId);
    }

    @Column(name="tariff_class")
    public Integer getTariffClass() {
        return this.tema.getTariffClass();
    }

    public void setTariffClass(Integer tariffClass) {
        this.tema.setTariffClass(tariffClass);
    }

    @Column(name="sell_only_on_bundle")
    public Boolean getSellOnlyOnBundle() {
        return this.tema.getSellOnlyOnBundle();
    }

    public void setSellOnlyOnBundle(Boolean sellOnlyOnBundle) {
        this.tema.setSellOnlyOnBundle(sellOnlyOnBundle);
    }

    @Column(name="motivo")
    public String getMotivo() {
        return this.exMotivo.getMessage();
    }

    public void setMotivo(String motivo) {
        this.exMotivo = new Exception(motivo);
    }

    @Column(name="fecha")
    @Temporal(javax.persistence.TemporalType.DATE)
    public Date getFecha() {
        return this.fecha;
    }

    public void setFecha(Date fecha) {
        this.fecha = fecha;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash = (this.getId() != null ? this.getId().hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof TemaErroneo)) {
            return false;
        }
        TemaErroneo other = (TemaErroneo) object;
        if ((this.getId() == null && other.getId() != null) || (this.getId() != null && !this.getId().equals(other.getId()))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "hpsynchronizer.TemaErroneo[temaErroneoPK=" + this.getId() + "]";
    }

}
