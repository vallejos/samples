package com.globalnet.util;

public class Filter {

	private String sc = null;
	private String subject = null;
	private String message = null;
	private String sourceAddress = null;
	
	public static final String EMPTY = "{[$none$]}";
	
	public Filter(String sc, String subject, String message, String sourceAddress) {
		this.sc = sc;
		this.message = message;
		this.sourceAddress = sourceAddress;
		
		if (!subject.equalsIgnoreCase(EMPTY))
			this.subject = subject;
	}
	
	public String getMessage() {
		return message;
	}
	
	public void setMessage(String message) {
		this.message = message;
	}
	
	public String getSc() {
		return sc;
	}
	
	public void setSc(String sc) {
		this.sc = sc;
	}
	
	public String getSourceAddress() {
		return sourceAddress;
	}
	
	public void setSourceAddress(String sourceAddress) {
		this.sourceAddress = sourceAddress;
	}
	
	public String getSubject() {
		return subject;
	}
	
	public void setSubject(String subject) {
		this.subject = subject;
	}
	
}
