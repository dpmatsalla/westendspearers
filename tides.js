function nextTide() {
    let nextTide = document.getElementById('nextTide');

    nextTide.innerHTML = "Next Tide";
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

    // draw noon vertical lines
    ctx.beginPath();
    ctx.strokeStyle = "blue";
    ctx.lineWidth = 0.5;
    ctx.rect((noon - timeStart)*xx/duration, yy - 3*amp, day*xx/duration, 3*amp);
    ctx.stroke();

    // draw midnight vertical lines
    ctx.beginPath();
    ctx.strokeStyle = "blue";
    ctx.lineWidth = 1;
    ctx.rect((midnight - timeStart)*xx/duration, yy - 3*amp, day*xx/duration, 3*amp);
    ctx.stroke();

    // draw 5am boxes
    ctx.fillStyle = "#E0E0C0";
    ctx.fillRect((next5am - timeStart)*xx/duration, yy - 3*amp, 1*3600*1000*xx/duration, 3*amp);
    ctx.fillRect((next5am + day - timeStart)*xx/duration, yy - 3*amp, 1*3600*1000*xx/duration, 3*amp);

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
    ctx.fillText("Now", (now - timeStart)*xx/duration - 18, yy/2);
    ctx.fillStyle = 'blue';
    ctx.fillText("0:00", (midnight - timeStart)*xx/duration - 15, 15);
    ctx.fillText("12:00", (noon - timeStart)*xx/duration - 15, 15);
    ctx.fillStyle = 'brown';
    ctx.fillText("5:00", (next5am - timeStart)*xx/duration, yy-15);

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
