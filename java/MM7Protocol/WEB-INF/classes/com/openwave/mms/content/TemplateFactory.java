// EDITOR NOTE: Please set number of columns to 100 in ur editor.

package com.openwave.mms.content;

import java.io.IOException;
import java.io.InputStream;

import java.util.HashMap;
import java.util.Properties;

/**
 * This class creates concrete implementation objects for the 
 * <code>Template</code> interface.  Template characteristics can be defined
 * in a properties file as follows: 
 *
 * <ul>
 * <li> content.template.1.name = textontop
 * <li> content.template.1.text.top = 0
 * <li> content.template.1.text.left = 0
 * <li> content.template.1.text.width = 100%
 * <li> content.template.1.text.height = 70%
 * <li> content.template.1.text.fit = hidden
 * <li> content.template.1.image.top = 50%
 * <li> content.template.1.image.left = 0
 * <li> content.template.1.image.width = 100%
 * <li> content.template.1.image.height = 30%
 * <li> content.template.1.image.fit = hidden
 * <p></p>
 * <li> content.template.2.name = sidebyside
 * <li> content.template.2.image.top = 0
 * <li> content.template.2.image.left = 0
 * <li> content.template.2.image.width = 50%
 * <li> content.template.2.image.height = 100%
 * <li> content.template.2.image.fit = hidden
 * <li> content.template.2.text.top = 0
 * <li> content.template.2.text.left = 50%
 * <li> content.template.2.text.width = 50%
 * <li> content.template.2.text.height = 100%
 * <li> content.template.2.text.fit = hidden
 * </ul>
 *
 * <p>
 * The <code>initialize</code> method can be used to initialize this class
 * with properties read from an application-specific properties file. Then
 * the application can use <code>getTemplate</code> to access the templates.
 * </p>
 */

public final class TemplateFactory {

    /**
     * Private constructor for maintaining the singleton aspects of
     * this class. Client applications should use getInstance to get a
     * reference to this object. It reads the properties file, content.properties,
     * creates the templates defined in it, and fills the templateMap HashMap so that
     * it can be queried later by client applications using getTemplateByName.
     */
    private TemplateFactory() {
        templateMap = new HashMap();
        defaultTemplate = new DefaultTemplate();
    }

    /**
     * Initializes the template factory with properties defining templates.
     * Any existing named templates in the factory will be lost.
     *
     * @param properties The properties defining the templates.
     * @exception NumberFormatException There is an error parsing the property values.
     */
    public void initialize( Properties properties ) throws NumberFormatException {

        templateMap.clear();

        // loop until we read all the template properties. if properties with
        // content.template.1 as prefix are defined they are set as default. if
        // the client wants to use the default template provided in the API and
        // create new templates, they should define templates starting with prefix
        // content.template.2 and so on.
        for( int counter = 1; ; counter++ ) { // infinite loop
            String propPrefix = "content.template." + counter;
            String propName = propPrefix + ".name";
            String name = properties.getProperty( propName );
            if( name == null || name.length() == 0 ) {
                if( counter == 1 ) {
                    defaultTemplate = new DefaultTemplate();
                    continue; // props can start from 2 if they want to use the
                              // api default template
                } else {
                    break; // exit condition
                }
            }

            RegionAttributes textAttributes = new RegionAttributes();
            textAttributes.setTop( properties.getProperty( propPrefix + ".text.top" ) );
            textAttributes.setLeft( properties.getProperty( propPrefix + ".text.left" ) );
            textAttributes.setWidth( properties.getProperty( propPrefix + ".text.width" ) );
            textAttributes.setHeight( properties.getProperty( propPrefix + ".text.height" ) );
            textAttributes.setFit( properties.getProperty( propPrefix + ".text.fit" ) );

            RegionAttributes imageAttributes = new RegionAttributes();
            imageAttributes.setTop( properties.getProperty( propPrefix + ".image.top" ) );
            imageAttributes.setLeft( properties.getProperty( propPrefix + ".image.left" ) );
            imageAttributes.setWidth( properties.getProperty( propPrefix + ".image.width" ) );
            imageAttributes.setHeight( properties.getProperty( propPrefix + ".image.height" ) );
            imageAttributes.setFit( properties.getProperty( propPrefix + ".image.fit" ) );

            if( counter == 1 ) {
                defaultTemplate = new TemplateImpl( textAttributes, imageAttributes );
            }

            templateMap.put( name, new TemplateImpl( textAttributes, imageAttributes ) );
        }

    }

    /**
     * Returns an instance of the class.
     *
     * @return An instance of the class.
     */
    public static TemplateFactory getInstance() {
        return factory;
    }

    /**
     * Searches the template map and returns the instance whose name matches
     * the given name. Returns the default template if name is null.
     *
     * @param name The name of the template to return. The name must exactly match
     *        the property content.template.<n>.name defined in the properties file.
     *        Returns the default template if name is null.
     * @return The template whose name matches the given name.
     *         Returns null if the template of the given name is not found.
     */
    public Template getTemplate( String name ) {
        if( name == null ) {
            return defaultTemplate;
        }

        return ( Template ) templateMap.get( name );
    }

    /**
     * Creates and returns a new <code>Template</code> with the given text and
     * image attributes.
     *
     * @param name The name of the template. The name can be null, in which case it will
     *        not be put in a map for querying later.
     * @param textAttributes The attributes of the text region of the template.
     * @param imageAttributes The attributes of the image region of the template.
     * @return A template with the given text and image attributes.
     */
    public Template newTemplate( String name,
                                 RegionAttributes textAttributes,
                                 RegionAttributes imageAttributes ) {
        Template template = new TemplateImpl( textAttributes, imageAttributes );
        if( name != null && name.length() > 0 ) {
            templateMap.put( name, template );
        }

        return template;
    }

    private static TemplateFactory factory = new TemplateFactory();
    private HashMap templateMap; // map of template name to template instance
    private Template defaultTemplate;

}
