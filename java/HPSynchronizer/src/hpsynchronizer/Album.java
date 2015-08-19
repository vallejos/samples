/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package hpsynchronizer;

import java.io.Serializable;
import java.util.ArrayList;
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
import javax.persistence.JoinTable;
import javax.persistence.Lob;
import javax.persistence.ManyToMany;
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
@Table(name = "albums")
@NamedQueries({
    @NamedQuery(name = "Album.findAll", query = "SELECT a FROM Album a"),
    @NamedQuery(name = "Album.findById", query = "SELECT a FROM Album a WHERE a.id = :id"),
    @NamedQuery(name = "Album.findByReleaseDate", query = "SELECT a FROM Album a WHERE a.releaseDate = :releaseDate"),
    @NamedQuery(name = "Album.findByUpc", query = "SELECT a FROM Album a WHERE a.upc = :upc"),
    @NamedQuery(name = "Album.findByPrdLength", query = "SELECT a FROM Album a WHERE a.prdLength = :prdLength"),
    @NamedQuery(name = "Album.findByActivo", query = "SELECT a FROM Album a WHERE a.activo = :activo"),
    @NamedQuery(name = "Album.findByIdsello", query = "SELECT a FROM Album a WHERE a.idsello = :idsello"),
    @NamedQuery(name = "Album.findByBundleId", query = "SELECT a FROM Album a WHERE a.bundleId = :bundleId"),
    @NamedQuery(name = "Album.findByOrden", query = "SELECT a FROM Album a WHERE a.orden = :orden")})
public class Album implements Serializable {
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    @Column(name = "id")
    private Integer id;
    @Basic(optional = false)
    @Lob
    @Column(name = "prd_title")
    private String prdTitle;
    @Column(name = "release_date")
    @Temporal(TemporalType.DATE)
    private Date releaseDate;
    @Column(name = "upc")
    private String upc;
    @Column(name = "prd_length")
    @Temporal(TemporalType.TIME)
    private Date prdLength;
    @Column(name = "activo")
    private Boolean activo;
    @Column(name = "idsello")
    private Integer idsello;
    @Column(name = "bundle_id")
    private String bundleId;
    @Column(name = "orden")
    private Integer orden;
    @ManyToMany(cascade = CascadeType.ALL)
    @JoinTable(name = "albums_temas",
    joinColumns = {
       @JoinColumn(name = "idtema")},
    inverseJoinColumns = {
       @JoinColumn(name = "idalbum")})
    private List<Tema> temas;
    @ManyToMany(cascade = CascadeType.ALL)
    @JoinTable(name = "albums_generos",
    joinColumns = {
       @JoinColumn(name = "idgenero")},
    inverseJoinColumns = {
       @JoinColumn(name = "idalbum")})
    private List<Genero> generos;
    @ManyToMany(cascade = CascadeType.ALL)
    @JoinTable(name = "albums_artistas",
    joinColumns = {
       @JoinColumn(name = "idartista")},
    inverseJoinColumns = {
       @JoinColumn(name = "idalbum")})

    private List<Artista> artistas;

    public Album() {
        this.artistas = new ArrayList<Artista>();
        this.generos = new ArrayList<Genero>();
        this.temas = new ArrayList<Tema>();
    }

    public Album(Integer id) {
        this.id = id;
        this.artistas = new ArrayList<Artista>();
        this.generos = new ArrayList<Genero>();
        this.temas = new ArrayList<Tema>();
    }

    public Album(Integer id, String prdTitle) {
        this.id = id;
        this.prdTitle = prdTitle;
        this.artistas = new ArrayList<Artista>();
        this.generos = new ArrayList<Genero>();
        this.temas = new ArrayList<Tema>();
    }

    public void addTema(Tema tema) {
        this.temas.add(tema);
    }

    public void addArtista(Artista artista){
        this.artistas.add(artista);
    }

    void addGenero(Genero genero) {
        this.generos.add(genero);
    }

    public List<Artista> getArtistas() {
        return artistas;
    }

    public void setArtistas(List<Artista> artistas) {
        this.artistas = artistas;
    }

    public List<Genero> getGeneros() {
        return generos;
    }

    public void setGeneros(List<Genero> generos) {
        this.generos = generos;
    }

    public Integer getId() {
        return id;
    }

    public void setId(Integer id) {
        this.id = id;
    }

    public String getPrdTitle() {
        return prdTitle;
    }

    public void setPrdTitle(String prdTitle) {
        this.prdTitle = prdTitle;
    }

    public Date getReleaseDate() {
        return releaseDate;
    }

    public void setReleaseDate(Date releaseDate) {
        this.releaseDate = releaseDate;
    }

    public String getUpc() {
        return upc;
    }

    public void setUpc(String upc) {
        this.upc = upc;
    }

    public Date getPrdLength() {
        return prdLength;
    }

    public void setPrdLength(Date prdLength) {
        this.prdLength = prdLength;
    }

    public Boolean getActivo() {
        return activo;
    }

    public void setActivo(Boolean activo) {
        this.activo = activo;
    }

    public Integer getIdsello() {
        return idsello;
    }

    public void setIdsello(Integer idsello) {
        this.idsello = idsello;
    }

    public String getBundleId() {
        return bundleId;
    }

    public void setBundleId(String bundleId) {
        this.bundleId = bundleId;
    }

    public Integer getOrden() {
        return orden;
    }

    public void setOrden(Integer orden) {
        this.orden = orden;
    }

    public List<Tema> getTemas() {
        return temas;
    }

    public void setTemas(List<Tema> temas) {
        this.temas = temas;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (id != null ? id.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        if (!(object instanceof Album)) {
            return false;
        }
        Album other = (Album) object;
        if ((this.id == null && other.id != null) || (this.id != null && !this.id.equals(other.id))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "hpsynchronizer.Album[id=" + id + "]";
    }
}
