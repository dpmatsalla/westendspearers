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
    let nextTime = new Date();
    nextTime.setHours(12,0,0,0);   
    if (nextTime.getTime() <= Date.now()) {
        nextTime.setDate(nextTime.getDate() + 1);
}
    const noon = nextTime.getTime();
    nextTime = new Date();
    nextTime.setHours(0, 0, 0, 0);
    nextTime.setDate(nextTime.getDate() + 1);
    const midnight = nextTime.getTime();
    nextTime = new Date();
    nextTime.setHours(5,0,0,0);   
    if (nextTime.getTime() <= Date.now()) {
        nextTime.setDate(nextTime.getDate() + 1);
}
    const next5am = nextTime.getTime();

    ctx.clearRect(0, 0, xx, yy);

    // draw noon vertical lines
    ctx.beginPath();
    ctx.rect((noon - timeStart)*xx/duration, yy - 3*amp, day*xx/duration, 3*amp);
    ctx.strokeStyle = "blue";
    ctx.lineWidth = 0.5;
    ctx.stroke();

    // draw midnight vertical lines
    ctx.beginPath();
    ctx.rect((midnight - timeStart)*xx/duration, yy - 3*amp, day*xx/duration, 3*amp);
    ctx.strokeStyle = "blue";
    ctx.lineWidth = 1;
    ctx.stroke();

    // draw 5am boxes
    ctx.fillStyle = "#FFAAAA";
    ctx.fillRect((next5am - timeStart)*xx/duration, yy - 3*amp, 1*3600*1000*xx/duration, 3*amp);

    // draw horizontal lines 
    ctx.beginPath();
    ctx.rect(0, yy - 3*amp, xx, 2*amp);
    ctx.rect(0, yy - 2*amp, xx, 2*amp);
    ctx.strokeStyle = '#111';
    ctx.lineWidth = 1;
    ctx.stroke();

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
