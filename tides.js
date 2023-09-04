function tidesTable() {
    
    const zeroPad = (num, places) => String(num).padStart(places, '0');

    let windNow = zeroPad(windDirNow,3) + 'º ' + String(windSpeedNow) + 'kts';

    let nextTide = document.getElementById('nextTide');
    const day = 24*3600*1000;
    const currentDate = new Date();
    const now = currentDate.getTime();
    
    // get next5am timestamp
    let nextTime = new Date(now);
    nextTime.setHours(5,0,0,0);   
    let next5am = nextTime.getTime();
    if (next5am <= now) {
        next5am += day;
    }
    
    //find where 5am is in the forecast array
    var i5am=0, d=0;
    while(d < next5am) {
        d = new Date(forecast.hourly.time[i5am]).getTime();
        i5am++;
    }

    const temp24h = Math.round(forecast.hourly.temperature_2m[i5am]) + 'ºC';
    const temp48h = Math.round(forecast.hourly.temperature_2m[i5am+24]) + 'ºC';
    const winds24h = zeroPad(Math.round(forecast.hourly.winddirection_10m[i5am]/10)*10, 3) + 'º ' + Math.round(forecast.hourly.windspeed_10m[i5am]) + 'kts';
    const winds48h = zeroPad(Math.round(forecast.hourly.winddirection_10m[i5am+24]/10)*10, 3) + 'º ' + Math.round(forecast.hourly.windspeed_10m[i5am+24]) + 'kts';

    const brisbane = [-27.4698, 153.0251]; // Latitude and Longitude of Brisbane
    var times = SunCalc.getTimes(currentDate, ...brisbane);
    const sunrise1 = times.sunrise;
    const sunriseFormat1 = sunrise1.toLocaleTimeString('en-US', { timeZone: 'Australia/Brisbane', hour: 'numeric', minute: 'numeric' });
    times = SunCalc.getTimes(new Date(next5am), ...brisbane);
    const sunrise2 = times.sunrise;
    const sunriseFormat2 = sunrise2.toLocaleTimeString('en-US', { timeZone: 'Australia/Brisbane', hour: 'numeric', minute: 'numeric' });
    times = SunCalc.getTimes(new Date(next5am + day), ...brisbane);
    const sunrise3 = times.sunrise;
    const sunriseFormat3 = sunrise3.toLocaleTimeString('en-US', { timeZone: 'Australia/Brisbane', hour: 'numeric', minute: 'numeric' });

    nextTide.innerHTML = '<table><tr> \
          <th>Date</th> \
          <th>Tide</th> \
          <th>Wind</th> \
          <th>Temp</th> \
          <th>Sunrise</th> \
        </tr><tr> \
          <td>Now</td> \
          <td>' + tideText(now) + '</td> \
          <td>' + windNow + '</td> \
          <td>' + tempNow + '</td> \
          <td>' + sunriseFormat1 + '</td> \
        </tr><tr> \
          <td>' + formatDay(next5am) + '</td> \
          <td>' + tideText(next5am) + '</td> \
          <td>' + winds24h + '</td> \
          <td>' + temp24h + '</td> \
          <td>' + sunriseFormat2 + '</td> \
        </tr><tr> \
          <td>' + formatDay(next5am + day) + '</td> \
          <td>' + tideText(next5am + day) + '</td> \
          <td>' + winds48h + '</td> \
          <td>' + temp48h + '</td> \
          <td>' + sunriseFormat3 + '</td> \
      </tr></table>';
}

function drawCurve() {
    const canvas = document.getElementById('myCanvas');
    const ctx = canvas.getContext('2d');
    const amp = 80; // amplitude
    const xx = canvas.width;
    const yy = canvas.height;

    const day = 24*3600*1000;
    const currentDate = new Date();
    const now = currentDate.getTime();
    const timeStart = now - 7*day;  //1 week ago
    const days = 20;
    const duration = days*day;
    const timeEnd = timeStart + duration;

    // get next midnight & noon & 5am
    let nextTime = new Date(timeStart);
    nextTime.setHours(0, 0, 0, 0);
    const midnight = nextTime.getTime() + day;
    nextTime = new Date(timeStart);
    nextTime.setHours(12,0,0,0);
    let noon = nextTime.getTime();
    if (noon <= timeStart) {
        noon += day;
    }
    nextTime = new Date(timeStart);
    nextTime.setHours(5,0,0,0);   
    let next5am = nextTime.getTime();
    if (next5am <= timeStart) {
        next5am += day;
    }
    
    ctx.clearRect(0, 0, xx, yy);   //clear canvas

    //draw day and night
    const brisbane = [-27.4698, 153.0251]; // Latitude and Longitude of Brisbane
    nextTime.setDate(nextTime.getDate() - 1);
    var times = SunCalc.getTimes(nextTime, ...brisbane);
    var sunset1 = times.sunset.getTime();
    for (var i=0; i<=days; i++) {
        nextTime.setDate(nextTime.getDate() + 1);
        times = SunCalc.getTimes(nextTime, ...brisbane);
        var sunrise = times.sunrise;
        var sunrise2 = sunrise.getTime();
        var sunset2 = times.sunset.getTime();
        ctx.fillStyle = "#B0B090"; //night
        ctx.fillRect((sunset1 - timeStart)*xx/duration, 0, (sunrise2 - sunset1)*xx/duration, yy);
        ctx.fillStyle = "#f5f5dc"; //day
        ctx.fillRect((sunrise2 - timeStart)*xx/duration, 0, (sunset2 - sunrise2)*xx/duration, yy);

        // draw 5-6am paddling boxes
        if (sunrise.getHours() >= 6) {
            ctx.fillStyle = "#009000";  // night paddle
        } else if (sunrise.getHours() < 5) {
            ctx.fillStyle = "#7CFC00";  // daylight paddle
        } else {
            ctx.fillStyle = "#FFA07A";  // sunrise paddle
        }
        let x = next5am + (i-1)*day; //start at 5am
        let y = tideHeight(x);
        ctx.beginPath();
        ctx.moveTo((x - timeStart)*xx/duration, 0);
        ctx.lineTo((x - timeStart)*xx/duration, yy - y*amp);
        x += 1*3600*1000;           //add an hour to 6am
        y = tideHeight(x);
        ctx.lineTo((x - timeStart)*xx/duration, yy - y*amp);
        ctx.lineTo((x - timeStart)*xx/duration, 0);
        ctx.closePath();
        ctx.fill();

        sunset1 = sunset2;
    }

    // draw horizontal lines 
    ctx.beginPath();
    ctx.strokeStyle = 'grey';
    ctx.lineWidth = 1;
    ctx.rect(0, yy - 3*amp, xx, 2*amp);
    ctx.rect(0, yy - 2*amp, xx, 2*amp);
    ctx.stroke();

    // draw text
    ctx.font = "18px Arial";
    ctx.fillStyle = 'gray';
    ctx.fillText("3 m", 5, yy - 3*amp +18);
    ctx.fillText("2 m", 5, yy - 2*amp +18);
    ctx.fillText("1 m", 5, yy - amp +18);
    ctx.font = "12px Arial";
    ctx.fillStyle = 'red';
    //ctx.save();
    //ctx.translate((now - timeStart)*xx/duration - 12, yy/5);
    //ctx.rotate(Math.PI/2);
    ctx.fillText("Now", (now - timeStart)*xx/duration - 12, yy/5);
    //ctx.rotate(-Math.PI/2);
    //ctx.translate(-(now - timeStart)*xx/duration + 12, -yy/5);
    //ctx.save();

    for (var i=0; i<days; i++) {
        // draw midnight vertical lines
        ctx.beginPath();
        ctx.strokeStyle = "blue";
        ctx.lineWidth = 2;
        ctx.moveTo((midnight + i*day - timeStart)*xx/duration, yy - 3*amp);  //replace
        ctx.lineTo((midnight + i*day - timeStart)*xx/duration, yy);
        ctx.stroke();
    
        // draw noon vertical lines
        ctx.beginPath();
        ctx.strokeStyle = "blue";
        ctx.lineWidth = 0.5;
        ctx.moveTo((noon + i*day - timeStart)*xx/duration, yy - 3*amp);  //replace
        ctx.lineTo((noon + i*day - timeStart)*xx/duration, yy);
        ctx.stroke();
    
        // text
        ctx.font = "bold 12px Arial";
        ctx.fillStyle = 'black';
        ctx.fillText("5-6am", (next5am + i*day - timeStart)*xx/duration - 13, 15);
        ctx.font = "12px Arial";
        ctx.fillStyle = 'blue';
        ctx.fillText("00:00", (midnight + i*day - timeStart)*xx/duration - 15, 15);
        ctx.fillText("12:00", (noon + i*day - timeStart)*xx/duration - 15, 15);

        ctx.font = "18px Arial";
        var nextDate = formatDay(noon + i*day);
        ctx.fillText(nextDate, (noon + i*day - timeStart)*xx/duration - 40, yy - 10);
    }
    
    // draw predicted tides, 15 min intervals 
    ctx.beginPath();
    for (let x = timeStart; x < timeEnd; x += 0.25*3600*1000) {
        let y = tideHeight(x);
        ctx.lineTo((x - timeStart)*xx/duration, yy - y*amp);
    }
    ctx.strokeStyle = '#000000';
    ctx.lineWidth = 3;
    ctx.stroke();

    // draw historical tides, 15 min intervals 
    ctx.beginPath();
    let x = timeStart;
    let r = 0;
    while (r < riverData.length) {
        x = riverData[r][0]*1000;
        y = parseFloat(riverData[r][1]) + 1;
        ctx.lineTo((x - timeStart)*xx/duration, yy - y*amp);
        r++;
    }
    ctx.strokeStyle = 'blue';
    ctx.setLineDash([2,1]);
    ctx.lineWidth = 4;
    ctx.stroke();

    // draw now line
    ctx.beginPath();
    ctx.moveTo((now - timeStart)*xx/duration, yy);
    ctx.lineTo((now - timeStart)*xx/duration, yy - 3*amp);
    ctx.strokeStyle = "red";
    ctx.setLineDash([]);
    ctx.lineWidth = 4;
    ctx.stroke();
    
    //adjust scrollbar
    document.getElementById('canvasScroll').scrollLeft = 3600 * 6.75 / 20;
}
tidesTable();
drawCurve();
