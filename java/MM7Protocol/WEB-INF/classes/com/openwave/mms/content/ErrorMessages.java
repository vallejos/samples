// EDITOR NOTE: Please set number of columns to 100 in ur editor.

package com.openwave.mms.content;

import java.util.ListResourceBundle;

/**
 * This class defines the default English locale error messages that
 * are used in this API. The API is shipped with a properties file
 * containing the error keys and values. The file can be localized
 * by the API users. The ContentException.setResourceBundle
 * method can be used to set localized resource bundles.
 */

final class ErrorMessages extends ListResourceBundle {

    public Object[][] getContents() {
        return contents;
    }

    private static final Object[][] contents = {
        { "messaging-exception",
          "MessagingException from JavaMail" },

        { "no-smil-directory",
          "smil file not found in directory" },

        { "no-smil-zip",
          "smil file not found in zip file" },

        { "no-smil-multipart",
          "smil part not found in multipart" },

        { "file-must-be-directory-zip-smil",
          "file argument must be a directory, zip file or smil file" },

        { "smil-validation-failed",
          "smil could not be validated" },

        { "smil-parsing-failed",
          "error parsing smil" },

        { "smil-creation-failed",
          "error creating smil" },

        { "cannot-parse-content-type",
          "cannot parse the Content-Type header value" },

        { "body-part-not-found",
          "cannot find the mime body part with content-id: " },

        { "only-multipart-allowed",
          "input stream must contain a mime multipart formatted message" }
    };

}
