<?php
	echo $this->Default3->titleForLayout();
	$size = (integer)Configure::read('Module.Logtrace.total_duration') + 20;
?>

<h3>Memory usage</h3>
<div style="position: relative; width: <?php echo $size;?>px; height: 220px;">
 <canvas id="mem_graph" width="<?php echo $size;?>" height="220" 
   style="position: absolute; left: 0; top: 0; z-index: 0;"></canvas>
 <canvas id="mem_layer1" width="<?php echo $size;?>" height="220" 
   style="position: absolute; left: 0; top: 0; z-index: 1;"></canvas>
 <canvas id="mem_layer2" width="<?php echo $size;?>" height="220" 
   style="position: absolute; left: 0; top: 0; z-index: 2;"></canvas>
</div>
<div id="mem_datas"></div>
<hr>
<h3>Loading time</h3>
<div style="position: relative; width: <?php echo $size;?>px; height: 220px;">
 <canvas id="loadingtime_graph" width="<?php echo $size;?>" height="220" 
   style="position: absolute; left: 0; top: 0; z-index: 0;"></canvas>
 <canvas id="loadingtime_layer1" width="<?php echo $size;?>" height="220" 
   style="position: absolute; left: 0; top: 0; z-index: 1;"></canvas>
 <canvas id="loadingtime_layer2" width="<?php echo $size;?>" height="220" 
   style="position: absolute; left: 0; top: 0; z-index: 2;"></canvas>
</div>
<div id="loadingtime_datas"></div>
<script>
	var data = <?php echo json_encode($log);?>,
		map = [],
		total_duration = <?php echo (integer)Configure::read('Module.Logtrace.total_duration');?>, // en secondes
		now = Math.round(Date.now()/1000),
		max_mem = 0,
		max_loadingtime = 0;

	function initMap() {
		map = [];
		for (var i=0; i<total_duration; i++) {
			map.push({
				total_mem: 0,
				calls: 0,
				datas: []
			});
		}
	}
	
	function mapData() {
		for (var i=data.length-1; i>=0; i--) {
			var timestamp = data[i].timestamp;
				begin = timestamp - Math.floor(data[i].loading_time);
				
			if (now-timestamp > total_duration) {
				break;
			}
			if (data[i].mem_allocated > max_mem) {
				max_mem = data[i].mem_allocated;
			}
			if (data[i].loading_time > max_loadingtime) {
				max_loadingtime = data[i].loading_time;
			}
			for (var s=begin; s<timestamp; s++) {
				if (now-s < 0) {
					continue;
				}
				map[now-s].total_mem += data[i].mem_allocated;
				map[now-s].calls++;
				map[now-s].datas.push(data[i]);
			}
		}
	}
	
	function drawMem(canvasId) {
		var c = $(canvasId),
			ctx = c.getContext("2d"),
			height = $(canvasId).getHeight() - 20,
			width = $(canvasId).getWidth() - 20,
			step = (width / total_duration) < 1 ? 1 : Math.round(width / total_duration),
			x, y,
			nb_times = 20,
			time; // (-2)
	
		ctx.fillStyle = "#ff8c1a";
		
		for (var i=0; i<map.length; i++) {
			x = i * step;
			y = map[i].total_mem / max_mem * 100;
			ctx.fillRect(x+20, height-20 - y, step, y);
		}
		
		ctx.fillStyle = "#000000";
		ctx.font = "14px Arial";
		ctx.fillText("0 MB", 0, height);
		ctx.fillText(max_mem/2+" MB", 0, height/2);
		ctx.fillText(max_mem+" MB", 0, 10);
		
		for (i=1; i<nb_times; i++) {
			time = new Date((now - total_duration + (total_duration / nb_times * (nb_times - i)))*1000);
			ctx.fillText('|', total_duration / nb_times * i, height-15);
			ctx.fillText(formatTime(time), total_duration / nb_times * i - 25, height);
		}
	}
	
	function drawLoadingtime(canvasId) {
		var c = $(canvasId),
			ctx = c.getContext("2d"),
			height = $(canvasId).getHeight() - 20,
			width = $(canvasId).getWidth() - 20,
			step = (width / total_duration) < 1 ? 1 : Math.round(width / total_duration),
			x, y,
			nb_times = 20,
			time; // (-2)
	
		ctx.fillStyle = "#007acc";
		
		for (var i=0; i<map.length; i++) {
			x = i * step;
			y = (extractAverageLoadingTime(map[i].datas) / max_loadingtime * 100) * height / 100;
			ctx.fillRect(x+20, height-20 - y, step, y);
		}
		
		ctx.fillStyle = "#000000";
		ctx.font = "14px Arial";
		
		ctx.fillText("0 s", 0, height);
		ctx.fillText(Math.round(max_loadingtime/2*100)/100+" s", 0, height/2);
		ctx.fillText(Math.round(max_loadingtime*100)/100+" s", 0, 10);
		
		for (i=1; i<nb_times; i++) {
			time = new Date((now - total_duration + (total_duration / nb_times * (nb_times - i)))*1000);
			ctx.fillText('|', total_duration / nb_times * i, height-15);
			ctx.fillText(formatTime(time), total_duration / nb_times * i - 25, height);
		}
	}
	
	function extractAverageLoadingTime(data) {
		var result = 0, total = 0, i = 0;
		
		if (data.length > 0) {
			for (; i<data.length; i++) {
				total += data[i].loading_time;
			}
			result = Math.round(total / data.length * 100)/100;
		}
		
		return result;
	}
	
	function formatTime(time) {
		var hours, minutes, seconds;
		
		hours = time.getHours();
		minutes = time.getMinutes();
		seconds = time.getSeconds();
		
		return (hours<10 ? '0'+hours : hours)+':'+(minutes<10 ? '0'+minutes : minutes)+':'+(seconds<10 ? '0'+seconds : seconds);
	}
	
	initMap();
	mapData();
	drawMem('mem_graph');
	drawLoadingtime('loadingtime_graph');
	
	$('mem_layer2').observe('mousemove', function(event) {
		var c = $('mem_layer1'),
			x = event.clientX - c.getBoundingClientRect().left,
			ctx = c.getContext("2d"),
			time,
			width = $('mem_layer1').getWidth() - 20,
			height = $('mem_layer1').getHeight() - 20,
			step = (width / total_duration) < 1 ? 1 : Math.round(width / total_duration);
			
		ctx.clearRect(0, 0, c.getWidth(), c.getHeight());
		ctx.fillStyle = "rgba(0, 0, 0, 0.3)";
		ctx.fillRect(Math.ceil(x), 0 , 1, c.getHeight());
		time = new Date((now - ((x+1) * step))*1000);
		ctx.fillText(formatTime(time), x+2, height+20);
	});
	
	$('mem_layer2').observe('click', function(event) {
		var c = $('mem_layer2'),
			x = event.clientX - c.getBoundingClientRect().left,
			ctx = c.getContext("2d"),
			html = '',
			time,
			width = $('mem_layer2').getWidth() - 20,
			height = $('mem_layer2').getHeight() - 20,
			step = (width / total_duration) < 1 ? 1 : Math.round(width / total_duration);
			
		ctx.clearRect(0, 0, c.getWidth(), c.getHeight());
		ctx.fillStyle = "rgba(255, 0, 0, 0.3)";
		ctx.fillRect(Math.ceil(x), 0 , 1, c.getHeight());
		
		time = new Date((now - ((x+1) * step))*1000);
		ctx.fillText(formatTime(time), x+2, height+10);
		html += '<strong>'+formatTime(time)+'</strong>';
		for (var i=0; i<map[Math.ceil(x-20)].datas.length; i++) {
			html += '<div style="padding-top: 5px">'+map[Math.ceil(x-20)].datas[i].text+'</div>';
		}
		$('mem_datas').innerHTML = html;
	});
	
	$('loadingtime_layer2').observe('mousemove', function(event) {
		var c = $('loadingtime_layer1'),
			x = event.clientX - c.getBoundingClientRect().left,
			ctx = c.getContext("2d"),
			time,
			width = $('loadingtime_layer1').getWidth() - 20,
			height = $('loadingtime_layer1').getHeight() - 20,
			step = (width / total_duration) < 1 ? 1 : Math.round(width / total_duration);
			
		ctx.clearRect(0, 0, c.getWidth(), c.getHeight());
		ctx.fillStyle = "rgba(0, 0, 0, 0.3)";
		ctx.fillRect(Math.ceil(x), 0 , 1, c.getHeight());
		time = new Date((now - ((x+1) * step))*1000);
		ctx.fillText(formatTime(time), x+2, height+20);
	});
	
	$('loadingtime_layer2').observe('click', function(event) {
		var c = $('loadingtime_layer2'),
			x = event.clientX - c.getBoundingClientRect().left,
			ctx = c.getContext("2d"),
			html = '',
			time,
			width = $('loadingtime_layer2').getWidth() - 20,
			height = $('loadingtime_layer2').getHeight() - 20,
			step = (width / total_duration) < 1 ? 1 : Math.round(width / total_duration);
			
		ctx.clearRect(0, 0, c.getWidth(), c.getHeight());
		ctx.fillStyle = "rgba(255, 0, 0, 0.3)";
		ctx.fillRect(Math.ceil(x), 0 , 1, c.getHeight());
		
		time = new Date((now - ((x+1) * step))*1000);
		ctx.fillText(formatTime(time), x+2, height+10);
		html += '<strong>'+formatTime(time)+'</strong>';
		for (var i=0; i<map[Math.ceil(x-20)].datas.length; i++) {
			html += '<div style="padding-top: 5px">'+map[Math.ceil(x-20)].datas[i].text+'</div>';
		}
		$('loadingtime_datas').innerHTML = html;
	});
	
</script>