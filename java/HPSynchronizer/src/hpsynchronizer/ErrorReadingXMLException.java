/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package hpsynchronizer;

/**
 *
 * @author fernando
 */
class ErrorReadingXMLException extends Exception {
    private String msg;

    ErrorReadingXMLException(String string) {
        this.msg = string;
    }

    @Override
    public String getMessage() {
        return this.msg;
    }
}
