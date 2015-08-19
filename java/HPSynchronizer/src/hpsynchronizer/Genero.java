/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package hpsynchronizer;

import java.io.Serializable;
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

/**
 *
 * @author leon
 */
@Entity
@Table(name = "generos")
@NamedQueries({
    @NamedQuery(name = "Genero.findAll", query = "SELECT g FROM Genero g"),
    @NamedQuery(name = "Genero.findById", query = "SELECT g FROM Genero g WHERE g.id = :id"),
    @NamedQuery(name = "Genero.findByNombre", query = "SELECT g FROM Genero g WHERE g.nombre = :nombre"),
    @NamedQuery(name = "Genero.findByPadre", query = "SELECT g FROM Genero g WHERE g.padre = :padre"),
    @NamedQuery(name = "Genero.findByActivo", query = "SELECT g FROM Genero g WHERE g.activo = :activo"),
    @NamedQuery(name = "Genero.findByNombreEs", query = "SELECT g FROM Genero g WHERE g.nombreEs = :nombreEs"),
    @NamedQuery(name = "Genero.findByIdgrupo", query = "SELECT g FROM Genero g WHERE g.idgrupo = :idgrupo")})
public class Genero implements Serializable {
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    @Column(name = "id")
    private Integer id;
    @Basic(optional = false)
    @Column(name = "nombre")
    private String nombre;
    @Column(name = "padre")
    private Boolean padre;
    @Column(name = "activo")
    private Boolean activo;
    @Column(name = "nombre_es")
    private String nombreEs;
    @Basic(optional = false)
    @Column(name = "idgrupo")
    private int idgrupo;
    @ManyToMany(mappedBy="generos", cascade = CascadeType.ALL)
    @JoinColumn(name="idalbum")
    private List<Album> albums;

    public Genero() {
    }

    public Genero(Integer id) {
        this.id = id;
    }

    public Genero(Integer id, String nombre, int idgrupo) {
        this.id = id;
        this.nombre = nombre;
        this.idgrupo = idgrupo;
    }

    public List<Album> getAlbums() {
        return albums;
    }

    public void setAlbums(List<Album> albums) {
        this.albums = albums;
    }

    public Integer getId() {
        return id;
    }

    public void setId(Integer id) {
        this.id = id;
    }

    public String getNombre() {
        return nombre;
    }

    public void setNombre(String nombre) {
        this.nombre = nombre;
    }

    public Boolean getPadre() {
        return padre;
    }

    public void setPadre(Boolean padre) {
        this.padre = padre;
    }

    public Boolean getActivo() {
        return activo;
    }

    public void setActivo(Boolean activo) {
        this.activo = activo;
    }

    public String getNombreEs() {
        return nombreEs;
    }

    public void setNombreEs(String nombreEs) {
        this.nombreEs = nombreEs;
    }

    public int getIdgrupo() {
        return idgrupo;
    }

    public void setIdgrupo(int idgrupo) {
        this.idgrupo = idgrupo;
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
        if (!(object instanceof Genero)) {
            return false;
        }
        Genero other = (Genero) object;
        if ((this.id == null && other.id != null) || (this.id != null && !this.id.equals(other.id))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "hpsynchronizer.Genero[id=" + id + "]";
    }

}
