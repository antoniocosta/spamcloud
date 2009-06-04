<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Spam Cloud</title>
	<script src="js/jquery-1.3.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.bt.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.hoverIntent.minified.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.bgiframe.min.js" type="text/javascript" charset="utf-8"></script>
	<!--[if IE]><script src="js/excanvas-compressed.js" type="text/javascript" charset="utf-8"></script><![endif]-->
	<script type="text/javascript">		
		$(document).ready(function(){
			$('.word').bt({
	//			ajaxPath: ["$(this).parent().attr('title')", 'div'],
				ajaxPath: ["$(this).data('origTitle')", 'div'],
				ajaxCache: false, 
				ajaxError: "Ajax Error: %error.",
				animate: true,
				positions: 'bottom',
				fill: '#000', 
				strokeStyle: '#fff',
				strokeWidth: 2, 
				spikeLength: 25, 
				spikeGirth: 20, 
				padding: 25, 
				cornerRadius: 12,
				width: 400,
				closeWhenOthersOpen: true,
				preShow: function() { // remove titles to avoid tooltip
					$(this).data('origTitle', $(this).parent().attr('title'));
					$(this).parent().removeAttr("title");
				},
				postHide: function() {
					$(this).parent().attr('title', $(this).data('origTitle'));
				},
				cssStyles: { width: 'auto', color:' #fff', fontSize: '12px' }
			});
		});
	</script>
	<style type="text/css" media="all">
		/*<![CDATA[*/
		body { font-size: 10px; text-align: left; }
		a, a:visited { color: #000; font-weight: bold;  text-decoration: underline; }
		a:active, a:hover { text-decoration: none; }
		#cloud { text-align: center; line-height: 14px; word-spacing: 0; }
		a.word {color: #000; font-weight: normal;  text-decoration: none; padding: 0.1em 0.1em 0em 0.1em; vertical-align: middle; text-decoration: none; filter:alpha(opacity=100); opacity: 1; -moz-opacity:1;}
		a:visited.word { color: #000; }
		a:active.word, a:hover.word { color: #ffffff; background: #000; }
		#footer { margin-top: 50px;}
		/*]]>*/
	</style>
	<style type="text/css" media="screen">
		/*<![CDATA[*/
		body { margin: 35px; }
		/*]]>*/
	</style>
	<style type="text/css" media="print">
		/*<![CDATA[*/
		body { margin: 0; }
		/*]]>*/
	</style>

</head>
<body>
	<div id="cloud">
		<?=$cloud_html?>
	</div>
	<div id="footer">
		<p><b>Spam Cloud: The real mullet of "the internets"</b> by Antonio Costa for <a href="http://prntscrn.org">prnt srcn</a> magazine issue #000000. Page generated in <?=$exec_time?> seconds from <?=$total_records?> spam messages, averaging <?=$records_per_day?> spam messages per day, received on personal email account over a period of <?=$total_period?>, from <?=$first_record_date?> to <?=$last_record_date?>. Mashed up from Gmail Atom feed, PHP, SimplePie, TextDB and jQuery. Released open source under GNU General Public License. Live demo: <a href="http://www.specialdefects.com/spamcloud">specialdefects.com/spamcloud</a> / Source code: <a href="http://code.google.com/p/spamcloud">code.google.com/p/spamcloud</a></p>
	</div>
</body>
</html>