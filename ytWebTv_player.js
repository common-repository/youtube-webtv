    var ytWebTv_playlist_totitems;
    var ytWebTv_playlist;
    var ytWebTv_playlist_player;
    var ytWebTv_playlist_start;
    var API_KEY = "AIzaSyDvpa0eBKcsPvoGe17m68SRNwn9dAMgCak";
    var tag = document.createElement('script');
    tag.src = "http://www.youtube.com/player_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    function timeToSeconds(time) {
    	var n = time.split(":");
    	return (parseInt(n[0]) * 3600) + (parseInt(n[1]) * 60) + parseInt(n[2]);
    }

// Manage player events 
    function onPlayerReady(event) {
    	event.target.loadVideoById({
    		'videoId': ytWebTv_playlist.videos[ytWebTv_playlist_index].id,
    		'startSeconds': ytWebTv_playlist_start
    	});
    	event.target.playVideo();
    }

    function onPlayerFinished(event) {
    	if (video_current_status == 2) {
    		$j("#ytWebTv_player").css("display", "none");
    	}
    }

    function onPlayerStateChange(event) {
    	if (event.data === 0) {
    		if ((ytWebTv_playlist_index + 1) > ytWebTv_playlist_totitems) {
    			ytWebTv_playlist_index = 0;
    		} else {
    			ytWebTv_playlist_index++;
    		}
    		event.target.loadVideoById(ytWebTv_playlist.videos[ytWebTv_playlist_index].id);
    		event.target.playVideo();
    	}
    }

// Playlist Loader
    function carica_ytWebTv_playlist(channel) {
    	ora = new Date();
    	ytWebTv_playlist_duration = 0;
    	for (var i in ytWebTv_playlist.videos) {
    		ytWebTv_playlist_duration += timeToSeconds(ytWebTv_playlist.videos[i].duration);
    	}
    	ytWebTv_playlist_totitems = i;
    	offset = timeToSeconds(ora.getHours() + ":" + ora.getMinutes() + ":" + ora.getSeconds()) % ytWebTv_playlist_duration;
    	offset_check = 0;
    	for (var i in ytWebTv_playlist.videos) {
    		offset_check += timeToSeconds(ytWebTv_playlist.videos[i].duration);
    		if (offset < offset_check) {
    			offset_check -= timeToSeconds(ytWebTv_playlist.videos[i].duration);
    			start = (offset - offset_check);
    			ytWebTv_playlist_index = i;
    			ytWebTv_playlist_start = start;
    			ytWebTv_playlist_player = new YT.Player('ytWebTv_player', {
    				height: ytWebTv_playlist.height,
    				width: ytWebTv_playlist.width,
    				events: {
    					'onReady': onPlayerReady,
    					'onStateChange': onPlayerStateChange,
    				}
    			});
    			break;
    		}
    	}
    }

    function onYouTubePlayerAPIReady() {
    	carica_ytWebTv_playlist(ytWebTv_channel);
    };