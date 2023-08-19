// Incorporate historical heights of brisbane river in the horizontal graph
// http://www.bom.gov.au/fwo/IDQ65389/IDQ65389.540683.tbl.shtml
// http://www.bom.gov.au/fwo/IDQ65389/IDQ65389.540683.plt.shtml


function formatDay(timestamp) {
  const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

  const date = new Date(timestamp);
  const dayOfWeek = daysOfWeek[date.getDay()];
  const month = months[date.getMonth()];
  const day = date.getDate();

  return `${dayOfWeek} ${day} ${month}`;
}

function nextTide() {
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

    const brisbane = [-27.4698, 153.0251]; // Latitude and Longitude of Brisbane
    var times = SunCalc.getTimes(currentDate, ...brisbane);
    const sunrise1 = times.sunrise;
    const sunriseFormat1 = sunrise1.toLocaleTimeString('en-US', { timeZone: 'Australia/Brisbane', hour: 'numeric', minute: 'numeric' });
    times = SunCalc.getTimes(new Date(next5am + day), ...brisbane);
    const sunrise2 = times.sunrise;
    const sunriseFormat2 = sunrise2.toLocaleTimeString('en-US', { timeZone: 'Australia/Brisbane', hour: 'numeric', minute: 'numeric' });


    nextTide.innerHTML = '<table><tr> \
          <th>Date-5am</th> \
          <th>Tide</th> \
          <th>Wind</th> \
          <th>Temp</th> \
          <th>Sunrise</th> \
        </tr><tr> \
          <td>Now</td> \
          <td>' + tideText(now) + '</td> \
          <td><?php echo $windDir."º ".$windSpeed." kts"; ?></td> \
          <td><?php echo $temp."ºC"; ?></td> \
          <td></td> \
        </tr><tr> \
          <td>' + formatDay(next5am) + '</td> \
          <td>' + tideText(next5am) + '</td> \
          <td></td> \
          <td></td> \
          <td>' + sunriseFormat1 + '</td> \
        </tr><tr> \
          <td>' + formatDay(next5am + day) + '</td> \
          <td>' + tideText(next5am + day) + '</td> \
          <td></td> \
          <td></td> \
          <td>' + sunriseFormat2 + '</td> \
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

    ctx.clearRect(0, 0, xx, yy);

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
    ctx.fillText("Now", (now - timeStart)*xx/duration - 12, yy/5);

    for (var i=0; i<days; i++) {
        // draw midnight vertical lines
        ctx.beginPath();
        ctx.strokeStyle = "blue";
        ctx.lineWidth = 1;
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
    
        // draw 5am boxes
        ctx.fillStyle = "#E0E0C0";
        ctx.fillRect((next5am + i*day - timeStart)*xx/duration, yy - 3*amp, 1*3600*1000*xx/duration, 3*amp);

        // text
        ctx.font = "12px Arial";
        ctx.fillStyle = 'brown';
        ctx.fillText("5-6am", (next5am + i*day - timeStart)*xx/duration - 13, 15);
        ctx.fillStyle = 'blue';
        ctx.fillText("00:00", (midnight + i*day - timeStart)*xx/duration - 15, 15);
        ctx.fillText("12:00", (noon + i*day - timeStart)*xx/duration - 15, 15);

        ctx.font = "18px Arial";
        var nextDate = formatDay(noon + i*day);
        ctx.fillText(nextDate, (noon + i*day - timeStart)*xx/duration - 40, yy - 10);
    }
    
    // draw tides, 15 min intervals 
    ctx.beginPath();
    for (let x = timeStart; x < timeEnd; x += 0.25*3600*1000) {
      const y = tideHeight(x);
      ctx.lineTo((x - timeStart)*xx/duration, yy - y*amp);
    }
    ctx.strokeStyle = '#000000';
    ctx.lineWidth = 3;
    ctx.stroke();

    // draw now line
    ctx.beginPath();
    ctx.moveTo((now - timeStart)*xx/duration, yy);
    ctx.lineTo((now - timeStart)*xx/duration, yy - 3*amp);
    ctx.strokeStyle = "red";
    ctx.lineWidth = 4;
    ctx.stroke();
}

nextTide();
drawCurve();
