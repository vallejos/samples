package com.globalnet.standalone;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.MalformedURLException;
import java.net.URL;

public class Tester {

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		// Patch by Leon & eltin, at 22/02/08
		// Invoke URL in order to fix corrupted images.
		URL fixer;
		try {
			fixer = new URL("http://10.0.0.250/image_fix/fix.php?operadora=claro_pe&id=1");
			BufferedReader in = new BufferedReader(new InputStreamReader(fixer.openStream()));

			String inputLine;
			while ((inputLine = in.readLine()) != null){
				System.out.println(inputLine);
			}
			in.close();
		
				
		} catch (MalformedURLException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		
		// May the hackzor be with you

	}

}
