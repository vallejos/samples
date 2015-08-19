
// this executes all the "window.onload" type events

$(document).ready(function(){

	// for export regulations
	$("#export-regulations").expander({
		slicePoint: 350,
					  widow: 1,
					  userCollapse: true,
					  expandText: 'Read full export regulations <img src="/static/images/arrow_down.png">',
					  userCollapseText: 'Hide full export regulations <img src="/static/images//arrow_up.png">'
	});

	// setup ul.tabs to work as tabs for each div directly under div.panes
	// added .history() to remember history of last tab
	$("ul.tabs").tabs("div.panes > div").history();

	// hide all "learn more" links
	$("span.learn-more").hide();

	// toggles links on/off on hover
	$("tr.spin-row").hover(
	function () {
		$(this).find("span.learn-more").show();
	},
			     function () {
				     $(this).find("span.learn-more").hide();
			     }
			     );

			     // force overlay
			     $("img[rel]").overlay();

			     // main site banners
			     // see: banner.js
			     var choices = [];
			     var k = 0;
			     for (var i = 0; i < banners.length; ++i) {
				     for (var j = 0; j < banners[i][3]; ++j) { choices[k++] = i; }
			     }

			     var choice = Math.floor(Math.random()*(choices.length));
			     var b_image = banners[choices[choice]][0];
			     var b_alt = banners[choices[choice]][1];
			     var b_url = banners[choices[choice]][2];

			     var b_bannerlink = document.getElementById("banner").getElementsByTagName("a")[0];
			     b_bannerlink.setAttribute("href", b_url);

			     var b_bannerimg = document.getElementById("banner").getElementsByTagName("img")[0];
			     b_bannerimg.setAttribute("src", b_image);
			     b_bannerimg.setAttribute("alt", b_alt);


			     // hosting sponsor banners
			     // see: /sponsors/*.js
			     var s_image = sponsor_banner[0];
			     var s_alt = sponsor_banner[1];
			     var s_url = sponsor_banner[2];

			     var s_bannerlink = document.getElementById("hosting-sponsor").getElementsByTagName("a")[0];
			     s_bannerlink.setAttribute("href", s_url);

			     var s_bannerimg = document.getElementById("hosting-sponsor").getElementsByTagName("img")[0];
			     s_bannerimg.setAttribute("src", s_image);
			     s_bannerimg.setAttribute("alt", s_alt);

});