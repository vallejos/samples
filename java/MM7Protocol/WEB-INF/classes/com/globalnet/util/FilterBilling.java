package com.globalnet.util;

public class FilterBilling {

	private String sc = null;
	private String subject = null;
	private String urlBilling = null;
	private String sourceAddress = null;
	
	public static final String EMPTY = "{[$none$]}";
	
	public FilterBilling(String sc, String subject, String url, String sourceAddress) {
		this.sc = sc;
		this.urlBilling = url;
		this.sourceAddress = sourceAddress;
		
		if (!subject.equalsIgnoreCase(EMPTY))
			this.subject = subject;
	}
	
	public String getURLBilling() {
		return urlBilling;
	}
	
	public void setURLBilling(String url) {
		this.urlBilling = url;
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
