<script>
var ytWebTv_plugins_url = "<?PHP echo plugins_url(); ?>";
var ytWebTv_admin_url = "<?PHP echo plugins_url(); ?>";
var ytWebTv_playlist_durata;
var ytWebTv_playlist_index;
var last_video_stream = '';
var ytWebTv_channel = "<?PHP echo $GLOBALS["yt_webtv_channel"]; ?>";


// Delete, Add, Save channel's informations
function ytWebTv_playlist_delete(id) {
	if (confirm('Vuoi cancellare?')) {
		$j('.ytWebTv_playlist_' + id).remove();
		ytWebTv_playlist_save();
	}
}

function ytWebTv_playlist_save() {
	var json_out = "";
	var width = $j("#ytWebTv_width").val();
	var height = $j("#ytWebTv_height").val();
	json_out = '{"id" :"' + ytWebTv_channel + '", "width":"' + width + '","height":"' + height + '", "videos" : [';
	$j('#ytWebTv_videoTable > tbody  > tr').each(function(item) {
		val = $j(this).find('.ytWebTv_playlist_id').html();
		val = val.replace('ytWebTv_playlist_', '');
		duration = $j(this).find('.ytWebTv_playlist_duration').html();
		title = $j(this).find('.ytWebTv_playlist_title').html();
		image = $j(this).find('.ytWebTv_playlist_image').attr("src");
		json_out = json_out + '{"id":"' + val + '","duration":"' + duration + '","title":"' + title.replace(/"/g, '') + '","image":"' + image + '"}';
		if (($j(".odd").size() - 1) != item) {
			json_out = json_out + ",";
		}
	});
	json_out = json_out + "]}";
	$j.post(ytWebTv_admin_url + "admin.php?page=yt_webtv_options", {
		id: ytWebTv_channel,
		data: json_out
	}).done(function(data) { });
}

function ytWebTv_playlist_add() {
	video = $j("#ytWebTv_playlist_add_text").val();
	if (video.indexOf("v=") > 0) {
		video_id = video.split('v=')[1];
		ytWebTv_playlist_additem(video_id);
	}
	$j("#ytWebTv_playlist_add_text").val("");
}

function ytWebTv_playlist_additem_html(id, title, duration, image) {
	$j("#ytWebTv_videoTable").append("<tr class='ytWebTv_playlist_" + id + " odd'><td width='70' valign=top><img src='" + image + "' class='ytWebTv_playlist_image' style='height:50px;'></td><td style='font-size:12px;'><div class='ytWebTv_playlist_id' style='display:none;'>" + id + "</div><b><div class='ytWebTv_playlist_title'>" + title + "</div></b><div class='ytWebTv_playlist_duration'>" + duration + "</div></td><td width='30'><a href='javascript:ytWebTv_playlist_delete(\"" + id + "\");' ><img border=0 style='width:15px;' src='" + ytWebTv_plugins_url + "/youtube-webtv/delete.png'></a></td></tr>");
}

function ytWebTv_playlist_additem(id) {
	$j.getJSON("https://www.googleapis.com/youtube/v3/videos?id=" + id + "&part=contentDetails,snippet&key=AIzaSyDvpa0eBKcsPvoGe17m68SRNwn9dAMgCak&callback=?", function(json) {
		duration = json.items[0].contentDetails.duration;
		duration = duration.replace("PT", "");
		if (duration.indexOf("H") > 0) {
			hours = duration.substring(0, duration.indexOf("H"));
			duration = duration.substring(duration.indexOf("H") + 1);
		} else {
			hours = 0;
		}
		if (duration.indexOf("M") > 0) {
			minutes = duration.substring(0, duration.indexOf("M"));
			duration = duration.substring(duration.indexOf("M") + 1);
		} else {
			minutes = 0;
		}
		if (duration.indexOf("S") > 0) {
			seconds = duration.substring(0, duration.indexOf("S"));
			duration = duration.substring(duration.indexOf("S") + 1);
		} else {
			seconds = 0;
		}
		duration = hours + ":" + minutes + ":" + seconds;
		ytWebTv_playlist_additem_html(json.items[0].id, json.items[0].snippet.title, duration, json.items[0].snippet.thumbnails.default.url);
		ytWebTv_playlist_save();
	});
}

// Drag and drop table sorting 
function make_sortable() {
	$j("#ytWebTv_videoTable tbody").sortable({
		stop: function(event, ui) {
			ytWebTv_playlist_save();
		}
	});
}

// Show videos list
function ytWebTv_playlist_admin_view() {
	var json = '<?PHP echo str_replace("'
	","\
	'",get_option("yt_webtv_channel_".$GLOBALS["yt_webtv_channel"])); ?>';
	if (json != "") {
		ytWebTv_playlist_admin = JSON.parse(json);
		var cont = 0;
		for (var j in ytWebTv_playlist_admin.videos) {
			// ytWebTv_playlist_additem(ytWebTv_playlist_admin.videos[j].id);
			ytWebTv_playlist_additem_html(ytWebTv_playlist_admin.videos[j].id, ytWebTv_playlist_admin.videos[j].title, ytWebTv_playlist_admin.videos[j].duration, ytWebTv_playlist_admin.videos[j].image);
		}
		$j("#ytWebTv_width").val(ytWebTv_playlist_admin.width);
		$j("#ytWebTv_height").val(ytWebTv_playlist_admin.height);
	}
}

// Set player's parameters
function ytWebTv_setPlayer(x, y) {
	$j("#ytWebTv_width").val(x);
	$j("#ytWebTv_height").val(y);
	ytWebTv_playlist_save();
}

var $j = jQuery.noConflict();
$j(document).ready(function() {
	ytWebTv_playlist_admin_view();
	make_sortable();
	$j('.ytWebTv_number').keypress(function(event) {
		var key = event.which;
		if (!(key >= 48 && key <= 57)) event.preventDefault();
	});
});

	
 </script>
 <div style="float:left;display:inline;">
<h1>Youtube Web Tv by Felice Marra</h1>
<h3>It's not just a playlist. All visitors will see the same video syncronized with<br>other users so you can create a WebTv with no streaming server needed. 
 <br><br>Add your youtube's videos and put the [ytWebTv] shortcode into your page.</h3>
<input type='text' class="ytWebTv_playlist_add_text" id='ytWebTv_playlist_add_text'>
<input type='button' onclick='ytWebTv_playlist_add();' value="ADD YOUTUBE'S VIDEO URL">
<br><br>Player
<input type="text" class="ytWebTv_number" id="ytWebTv_width" name="ytWebTv_width" value="560">x
<input type="text" class="ytWebTv_number" id="ytWebTv_height" name="ytWebTv_height" value="315">
<input class="ytWebTv_preset" type="button" value="420x315" onclick="javascript:ytWebTv_setPlayer(420,315);">
<input class="ytWebTv_preset" type="button" value="560x315" onclick="javascript:ytWebTv_setPlayer(560,315);">
<input class="ytWebTv_preset" type="button" value="640x480" onclick="javascript:ytWebTv_setPlayer(640,480);">
<input class="ytWebTv_preset" type="button" value="853x360" onclick="javascript:ytWebTv_setPlayer(853,480);">
<input class="ytWebTv_preset" type="button" value="960x720" onclick="javascript:ytWebTv_setPlayer(960,720);">
<input class="ytWebTv_preset" type="button" value="1280x720" onclick="javascript:ytWebTv_setPlayer(1280,720);">
<br>
<br>
<table id="ytWebTv_videoTable"><tbody></tbody></table>
 </div>
 <div style="width:200px;float:right;display:inline;text-align:center;">
       <br><br>
       <h3>Help me to improve<br>this WebTv plugin</h3>
       <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="7GAGEQHLJ5P5L">
<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
</form>

 </div>

</body>