<?
/**
 * Spam Cloud
 *
 * Generates a "tag" cloud from gmail atom feed.
 *
 * Copyright (c) 2009, Antonio Costa
 * All rights reserved.
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @version 	0.1
 * @copyright 	2009 Antonio Costa
 * @author 		Antonio Costa
 * @link 		http://www.specialdefects.com/spamcloud
 * @license 	http://www.gnu.org/licenses/gpl-3.0-standalone.html
 *
 */
// ==============================================================
// = DESCRIPTION
// ==============================================================
//
//  Spam Cloud generates a "tag" cloud from a gmail atom feed. It reads 
//  and saves the titles from a gmail atom feed (or any other RSS feed for 
//	that matter) into a text database, and generates a "spam" cloud. 
//  
//  Experimental project developed by Antonio Costa - http://antoniocosta.eu - 
//  for prnt scrn magazine - http://prntscrn.org - issue #000000
//
//  A take on tag clouds, the role of email and internet culture in general. 
//  Described by some as the "mullet of the internet", a tag cloud is a 
//  visualization of word frequency on given text content, used typically to 
//  describe the content of a web site.
//  
//  Live demo at http://www.specialdefects.com/spamcloud
//  Source on http://code.google.com/p/spamcloud/
//  
//  Built on:
//  PHP - http://www.php.net
//  SimplePie - http://simplepie.org
//  TextDB - http://www.myupb.com/wiki/index.php/TextDB
//  jQuery - http://jquery.com
//  
//  Released open source under GNU General Public License.
//
// ==============================================================
// = USAGE
// ==============================================================
//
//  1. Edit the variables in CONFIG section below. Make sure your gmail login and password are correct.
//  2. Upload all files to your server
//  3. chmod 777 the db/ folder and all files within, so the server has write permissions
//  4. Setup a cron to get gmail RSS feed every 5 or 10 minutes and update the database.
//     Example for getting the RSS feed with wget every 5 minutes
//     */5 * * * * wget -O - -q http://www.yourdomain.com/spamcloud/index.php?cron >> /dev/null 2>&1
//
//  NOTES:
//  - index.php?cron will make the script get the gmail RSS feed and update the database
//  - At this point no records are deleted from the database, which may cause it to slow down when it gets too full
//
//

// ==============================================================
// = CONFIG
// ==============================================================

mb_internal_encoding('UTF-8');

// Edit you gmail login and password below:
$your_gmail_login = "login";
$your_gmail_password = "password";

$url = "https://".$your_gmail_login.":".$your_gmail_password."@mail.google.com/mail/feed/atom/spam"; // spam gmail atom/rss feed

// maximum records that the database will hold (older records will be deleted).
// Good for over a month of spam at my rate ;) Be aware that more records will make the script slower and use more server memory
$max_db_records = 4000;

$min_font_size = 10; // minimum font size in pixels
$max_font_size = 80;  // maximum font size in pixels

$min_word_length = 3;  //minimum length of single words
$min_word_occur = 2; // minimum occurences of word required for display

$word_limit = 750; // maximum number of words to show. 0 to disable

// these words won't show in the cloud
$common_words = array('able', 'about', 'above', 'act', 'add', 'after', 'again', 'against', 'ago', 'agree', 'all', 'almost', 'along', 'already', 'also', 'although', 'always', 'am', 'amount', 'an', 'and', 'another', 'any', 'appear', 'are', 'arrive', 'arm', 'arms', 'around', 'arrive', 'as', 'ask', 'at', 'attempt', 'aunt', 'away', 'back', 'bag', 'bay', 'be', 'became', 'because', 'become', 'been', 'before', 'began', 'begin', 'behind', 'being', 'bell', 'belong', 'below', 'beside', 'better', 'between', 'beyond', 'bone', 'born', 'borrow', 'both', 'bottom',  'boy', 'break', 'bring', 'brought', 'bug', 'built', 'busy', 'but', 'by', 'came', 'can', 'cause', 'choose', 'consider', 'consider', 'considerable', 'contain', 'continue', 'could', 'cry', 'cut', 'dare', 'deal', 'dear', 'decide', 'deep', 'did', 'die', 'do', 'does', 'done', 'dont', 'doubt', 'down', 'during', 'each', 'ear', 'early', 'eat', 'effort', 'either', 'else', 'end', 'enjoy', 'enough', 'enter', 'even', 'ever', 'every', 'except', 'expect', 'explain', 'fall', 'far', 'feet', 'fell', 'felt', 'few', 'fill', 'find', 'follow', 'for', 'forever', 'forget', 'from', 'front', 'gave', 'get', 'gets', 'give', 'gives', 'goes', 'gone', 'got', 'grew', 'had', 'half', 'hang', 'happen', 'has', 'hat', 'have', 'he', 'hear', 'heard', 'held', 'her', 'here', 'hers', 'hill', 'him', 'his', 'hit', 'hold', 'hot', 'how', 'however', 'I', 'if', 'ill', 'in', 'indeed', 'instead', 'into', 'iron', 'is', 'it', 'its', 'just', 'keep', 'kept', 'knew', 'know', 'known', 'late', 'least', 'led', 'left', 'lend', 'less', 'let', 'like', 'likely', 'lone', 'long', 'look', 'lot', 'make', 'making', 'many', 'may', 'me', 'mean', 'met', 'might', 'mile', 'mine', 'most', 'move', 'much', 'must', 'my', 'near', 'nearly', 'necessary', 'neither', 'never', 'next', 'no', 'none', 'nor', 'not', 'note', 'nothing', 'now', 'number', 'of', 'off', 'often', 'oh', 'on', 'once', 'only', 'or', 'other', 'ought', 'our', 'out', 'prepare', 'probable', 'pull', 'pure', 'push', 'put', 'raise', 'ran', 'rather', 'reach', 'ready', 'realize', 'really', 'reply', 'require', 'rest', 'run', 'said', 'same', 'sat', 'saw', 'say', 'see', 'seem', 'seen', 'self', 'sell', 'sent', 'separate', 'set', 'shall', 'she', 'should', 'side', 'sign', 'since', 'so', 'sold', 'some', 'soon', 'stay', 'step', 'stick', 'still', 'stood', 'such', 'sudden', 'suppose', 'sure', 'take', 'taken', 'talk', 'tall', 'tell', 'ten', 'than', 'thank', 'that', 'the', 'their', 'them', 'then', 'there', 'therefore', 'these', 'they', 'this', 'those', 'though', 'through', 'till', 'to', 'today', 'told', 'tomorrow', 'too', 'took', 'tore', 'toward', 'tried', 'tries', 'try', 'turn', 'two', 'under', 'until', 'up', 'upon', 'us', 'use', 'usual', 'various', 'verb', 'very', 'visit', 'want', 'was', 'we', 'well', 'went', 'were', 'what', 'when', 'where', 'whether', 'which', 'while', 'white', 'who', 'whom', 'whose', 'why', 'will', 'with', 'within', 'without', 'would', 'yes', 'yet', 'you', 'your', 'youve', 'youll', 'br', 'img', 'p','lt', 'gt', 'quot', 'copy', 'von', 'und');


// ==============================================================
// = FUNCTIONS
// ==============================================================

function microtime_float(){
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

function get_formatted_timediff($then, $now = false){
	
	$int_second = 1;
	$int_minute = 60;
	$int_hour 	= 3600;
	$int_day 	= 86400;
	$int_week 	= 604800;
	$now      = (!$now) ? time() : $now;
	$timediff = ($now - $then);
	
	if($timediff<0){ $timediff = 0; }

	$weeks    = (int) intval($timediff / $int_week);
	$timediff = (int) intval($timediff - ($int_week * $weeks));
	$days     = (int) intval($timediff / $int_day);
	$timediff = (int) intval($timediff - ($int_day * $days));
	$hours    = (int) intval($timediff / $int_hour);
	$timediff = (int) intval($timediff - ($int_hour * $hours));
	$mins     = (int) intval($timediff / $int_minute);
	$timediff = (int) intval($timediff - ($int_minute * $mins));
	$sec      = (int) intval($timediff / $int_second);
	$timediff = (int) intval($timediff - ($sec * $int_second));

	$str = '';
	if ( $weeks ){
		$str .= intval($weeks);
		$str .= ($weeks > 1) ? ' weeks' : ' week';
	}
	if ( $days ){
		$str .= ($str) ? ', ' : '';
		$str .= intval($days);
		$str .= ($days > 1) ? ' days' : ' day';
	}
	if ( $hours ){
		$str .= ($str) ? ', ' : '';
		$str .= intval($hours);
		$str .= ($hours > 1) ? ' hours' : ' hour';
	}
	if ( $mins ){
		$str .= ($str) ? ', ' : '';
		$str .= intval($mins);
		$str .= ($mins > 1) ? ' minutes' : ' minute';
	}
	if ( $sec ){
		$str .= ($str) ? ', ' : '';
		$str .= intval($sec);
		$str .= ($sec > 1) ? ' seconds' : ' second';
	}
	if ( !$weeks && !$days && !$hours && !$mins && !$sec ){
		$str .= '0 seconds';
	}
	return $str;
}


function clean_string($str = string){
	$str = mb_strtolower($str, "UTF-8"); //convert all characters to lower case
	$str = strip_tags($str);
	$str = html_entity_decode($str);
	$punctuations = array('@', ',', ')', '(', '.', "'", '"', '<', '>', ';', '!', '?', '/', '-', '_', '[', ']', ':', '+', '=', '#', '$', '*', '&quot;', '&', '&copy;', '&gt;', '&lt;', chr(10), chr(13), chr(9), '’');
	$str = str_replace($punctuations, '', $str);
	$str = preg_replace('/ {2,}/si', ' ', $str); // replace multiple spaces
	$str = stripslashes($str);
	return $str;
}

function parse_words($str = string, $common_words = array(), $min_word_length = number, $min_word_occur = number){
	
	$str = clean_string($str);
	$s = split(' ', $str); //create an array out of the contents
	$k = array(); //initialize array
	foreach( $s as $key=>$val ) { //iterate inside the array
		//Delete short words and if the word is contained in the common words list.
		if(mb_strlen(trim($val)) >= $min_word_length  && !in_array(trim($val), $common_words)  && !is_numeric(trim($val))) {
			$k[] = trim($val);
		}
	}
	$k = array_count_values($k); //count the words	
	
	$occur_filtered = array(); //sort the words from highest count to the lowest.
	foreach ($k as $word => $occured) {
		if ($occured >= $min_word_occur) {
			$occur_filtered[$word] = $occured;
		}
	}
	arsort($occur_filtered, SORT_NUMERIC);
	return $occur_filtered;
}


function cloud( $data = array(), $limit = 0, $min_font_size = 10, $max_font_size = 80 ){ // generate the tagcloud from the provided parsed data
	$min_count = @min( array_values( $data ) );
	$max_count = @max( array_values( $data ) );
	$spread = $max_count - $min_count;
	$spread == 0 && $spread = 1;
	$cloud_tags = array();
	
	if($limit > 0){
		$data = array_slice($data, 0, $limit); // limit the number of items to show
	}
	ksort($data); // sort array alphabetically

	foreach( $data as $tag => $count ){
		$size = floor( $min_font_size + ( $count - $min_count ) * ( $max_font_size - $min_font_size ) / $spread );

		$cloud_tags[] = '<span title="index.php?dyn&c='.$count.'&q='.$tag.'"><a style="font-size: '.$size.'px;" class="word" href="http://www.google.com/search?q='.$tag.'" title="'.$count.'">'.$tag.'</a></span>';

	}
	return implode( "\n", $cloud_tags ) . "\n";
}

// ==============================================================
// = DO STUFF!
// ==============================================================

$exec_time_start = microtime_float(); // count script execution time

if( substr($_SERVER["QUERY_STRING"],0,3) == 'dyn' ){ // javascript request. format: index.php?dyn?c=5&c=pills

	echo "<div>\n";
	$c = $_GET['c'];
	$q = $_GET['q'];
	if(empty($q)){
		die('ERROR: no query!');
	}else{
		echo '<b>'.$c.'* '.$q."</b><br><br>\n";
	}
	require('lib/tdb.class.php');
	$tdb = new tdb('', '');
	$tdb->tdb('./db/', 'database');
	$tdb->setFp('ta', 'spam');
	$records_arr = $tdb->query('ta', "title?'$q'");
	$tdb->cleanUp();
	if($records_arr==false){
		echo "ERROR: No results"; die('</div>');
	}
	$titles_arr = array();
	foreach($records_arr as $item_arr){
		$tmp_str = clean_string($item_arr['title']);
		$tmp_arr = explode(' ', $tmp_str); // get rid of false positives like 'again' when searching 'gain'
		foreach ($tmp_arr as $tmp_str){		
			if ($tmp_str == mb_strtolower($q) ){
				$titles_arr[] = $item_arr['title'];	//word exists in text
			}
		}
	}
	$titles_arr = array_count_values($titles_arr); // count
	arsort($titles_arr, SORT_NUMERIC); // sort descending	
	$titles_arr = array_slice($titles_arr, 0, 10); // limit number of items. displays fiorst 10 most frequent
	foreach($titles_arr as $title => $occured){
		echo stripslashes($title)." / \n";
	}
	echo '</div>';

}else if( $_SERVER["QUERY_STRING"] == 'cron' ){ // cron
	
	require_once('lib/simplepie.class.php');
	require_once('lib/tdb.class.php');
	$tdb = new tdb('', '');
	@$tdb->createDatabase('./db/', 'database');
	$tdb->tdb('./db/', 'database');
	@$tdb->createTable('spam', array(
								array('id', 'id'),
								array('md5', 'string', 32),
								array('timestamp', 'number', 10),
								array('title', 'memo')
								));

	$feed = new SimplePie();
	$raw_data = @file_get_contents($url);
	$feed->set_raw_data($raw_data);
	$feed->init();

	foreach ($feed->get_items() as $item){
		$md5 = $item->get_id(true); //md5
		$timestamp = $item->get_date('U');
		$title =  $item->get_title();

		$tdb->setFp('ta', 'spam');
		$result_arr = $tdb->basicQuery('ta', 'md5', $md5, 1, 1); // will retrieve all fields
		if(!isset($added)){ $added = 0; }
		if(!isset($skipped)){ $skipped = 0; }
		if(empty($result_arr) == true){
			$id = $tdb->add('ta', array('md5'=>$md5, 'timestamp'=>$timestamp, 'title'=>$title));
			$added++;
		}else{
			$skipped++;
		}
	}

	$total_records = $tdb->getNumberOfRecords('ta'); // get total records
	$total_records_deleted = 0;
	if($total_records > $max_db_records){ // Delete old records if the limit of records is passed
		$total_records_to_delete = $total_records - $max_db_records;
		$records_arr = $tdb->listRec('ta', 1, $total_records_to_delete);
		foreach($records_arr as $item_arr){
			$tdb->delete('ta', $item_arr['id']);
		}
		$total_records = $tdb->getNumberOfRecords('ta'); // get total records
		$total_records_deleted = $total_records_to_delete;
	}
	
	$first_record_arr = $tdb->listRec('ta', 1, 1); // get first record
	$last_record_arr = $tdb->listRec('ta', $total_records-1, 1); // get last record

	$tdb->cleanUp();	
	$first_record_date = date("l, jS F Y, H:i", $first_record_arr[0]['timestamp']);
	$last_record_date = date("l, jS F Y, H:i", $last_record_arr[0]['timestamp']);
	$total_period = get_formatted_timediff($first_record_arr[0]['timestamp'], $last_record_arr[0]['timestamp']);
	$records_per_day = round(($total_records / ( $last_record_arr[0]['timestamp'] - $first_record_arr[0]['timestamp']) ) *60*60*24, 0 );
	
	$exec_time = round(microtime_float()-$exec_time_start, 4); // count script execution time
	include('template_cron.php');

}else{ // display cloud
	
	require_once('lib/tdb.class.php');
	$tdb = new tdb('', '');
	$tdb->tdb('./db/', 'database');
	$tdb->setFp('ta', 'spam');
	$records_arr = $tdb->listRec('ta', 1); // get all records
	$spam = '';
	$spam_arr = array();
	foreach($records_arr as $item_arr){
		$spam .= $item_arr['title'].' ';
		$spam_arr[] = $item_arr['title'];
	}

	$total_records = $tdb->getNumberOfRecords('ta'); // get total records
	$first_record_arr = $tdb->listRec('ta', 1, 1); // get first record
	$last_record_arr = $tdb->listRec('ta', $total_records-1, 1); // get last record

	$tdb->cleanUp();

	$cloud_html = cloud( parse_words($spam, $common_words, $min_word_length, $min_word_occur), $word_limit, $min_font_size, $max_font_size );
	$first_record_date = date("l, jS F Y, H:i", $first_record_arr[0]['timestamp']);
	$last_record_date = date("l, jS F Y, H:i", $last_record_arr[0]['timestamp']);
	$total_period = get_formatted_timediff($first_record_arr[0]['timestamp'], $last_record_arr[0]['timestamp']);
	$records_per_day = round(($total_records / ( $last_record_arr[0]['timestamp'] - $first_record_arr[0]['timestamp']) ) *60*60*24, 0 );
	
	$exec_time = round(microtime_float()-$exec_time_start, 4); // count script execution time
	include('template_cloud.php');	
}


?>
