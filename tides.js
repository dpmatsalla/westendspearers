function tideText(t) {
    let t1 = tideHeight(t);
    let t0 = tideHeight(t - 1*3600*1000);
    let t2 = tideHeight(t + 1*3600*1000);
    let text = t1.toFixed(1) + ' m ';

    if (t1 > t0) {
        if (t2 > t1) {text += 'incoming';}
        else {text += 'high neutral';}
    } else {
        if (t2 < t1) {text += 'outgoing';}
        else {text += 'low neutral';}
    }
    return text;
}

function nextTide() {
    let nextTide = document.getElementById('nextTide');
    const day = 24*3600*1000;
    const currentDate = new Date();
    const now = currentDate.getTime();
    
    // get next5am timestamp
    let nextTime = new Date(now);
    nextTime.setHours(5,0,0,0);   
    if (nextTime.getTime() <= now) {
        nextTime.setDate(nextTime.getDate() + 1);
    }
    const next5am = nextTime.getTime();
    let nextDate =  nextTime.toLocaleDateString('en-US', { day: 'numeric', month: 'short' });

    let nextTime2 = new Date(now);
    nextTime2.setDate(nextTime.getDate() + 1);
    let nextDate2 =  nextTime2.toLocaleDateString('en-US', { day: 'numeric', month: 'short' });

    nextTide.innerHTML = '<table width="100%"><tr> \
          <th>Date/Time</th> \
          <th>Tide</th> \
          <th>Wind</th> \
        </tr><tr> \
          <td>Now</td> \
          <td>' + tideText(now) + '</td> \
          <td></td> \
        </tr><tr> \
          <td>' + nextDate + ', 5am</td> \
          <td>' + tideText(next5am) + '</td> \
          <td></td> \
        </tr><tr> \
          <td>' + nextDate2 + ', 5am</td> \
          <td>' + tideText(next5am + day) + '</td> \
          <td></td> \
      </tr></table>';
        
}

function drawCurve() {
    const canvas = document.getElementById('myCanvas');
    const ctx = canvas.getContext('2d');
    ctx.width = 400;
    const amp = 80; // amplitude
    const xx = canvas.width;
    const yy = canvas.height;

    const day = 24*3600*1000;
    const currentDate = new Date();
    const now = currentDate.getTime();
    const timeStart = now - 6*3600*1000;  //6hrs ago
    const duration = 2*day;
    const timeEnd = timeStart + duration;

    // get next midnight & noon & 5am
    let nextTime = new Date(timeStart);
    nextTime.setHours(0, 0, 0, 0);
    nextTime.setDate(nextTime.getDate() + 1);
    const midnight = nextTime.getTime();
    nextTime = new Date(timeStart);
    nextTime.setHours(12,0,0,0);   
    if (nextTime.getTime() <= timeStart) {
        nextTime.setDate(nextTime.getDate() + 1);
    }
    const noon = nextTime.getTime();
    nextTime = new Date(timeStart);
    nextTime.setHours(5,0,0,0);   
    if (nextTime.getTime() <= timeStart) {
        nextTime.setDate(nextTime.getDate() + 1);
    }
    const next5am = nextTime.getTime();

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

    for (var i=0; i<2; i++) {
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
        ctx.fillText("5-6am", (next5am + i*day - timeStart)*xx/duration - 15, 15);
        ctx.fillStyle = 'blue';
        ctx.fillText("00:00", (midnight + i*day - timeStart)*xx/duration - 15, 15);
        ctx.fillText("12:00", (noon + i*day - timeStart)*xx/duration - 15, 15);

        ctx.font = "18px Arial";
        nextTime = new Date(noon + i*day);
        var nextDate =  nextTime.toLocaleDateString('en-US', { day: 'numeric', month: 'short' });
        ctx.fillText(nextDate, (noon + i*day - timeStart)*xx/duration - 30, yy - 10);

    }
    
    // draw tides, 15 min intervals 
    ctx.beginPath();
    for (let x = timeStart; x < timeEnd; x += 0.25*3600*1000) {
      const y = amp*tideHeight(x);
      ctx.lineTo((x - timeStart)*xx/duration, yy - y);
    }
    ctx.strokeStyle = '#000';
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
