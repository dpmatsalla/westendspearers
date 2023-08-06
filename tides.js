function drawCurve() {
    const canvas = document.getElementById('myCanvas');
    const ctx = canvas.getContext('2d');
    const amp = 100; // amplitude
    const xx = canvas.width;
    const yy = canvas.height;

    const day = 24*3600*1000;
    const currentDate = new Date();
    const now = currentDate.getTime();
    const timeStart = now - 6*3600*1000;  //6hrs ago
    const duration = 2*day;
    const timeEnd = timeStart + duration;
    const midnight = (Math.round(timeStart/day) + 1)*day;
    
    ctx.clearRect(0, 0, xx, yy);

    // draw noon vertical lines
    ctx.beginPath();
    ctx.rect((midnight + day/2 - timeStart)*xx/duration, yy - 3*amp, day*xx/duration, 3*amp);
    ctx.strokeStyle = "blue";
    ctx.lineWidth = 0.5;
    ctx.stroke();

    // draw midnight vertical lines
    ctx.beginPath();
    ctx.rect((midnight - timeStart)*xx/duration, yy - 3*amp, day*xx/duration, 3*amp);
    ctx.strokeStyle = "blue";
    ctx.lineWidth = 1;
    ctx.stroke();

    ctx.beginPath();
    ctx.rect(0, yy - 3*amp, xx, 2*amp);
    ctx.rect(0, yy - 2*amp, xx, 2*amp);
    ctx.strokeStyle = '#111';
    ctx.lineWidth = 1;
    ctx.stroke();

    ctx.beginPath();
    for (let x = timeStart; x < timeEnd; x += 1*3600*1000) {
      const y = amp*tideHeight(x);
      ctx.lineTo((x - timeStart)*xx/duration, yy - y);
    }
    ctx.strokeStyle = '#000';
    ctx.lineWidth = 2;
    ctx.stroke();

    ctx.beginPath();
    ctx.moveTo((now - timeStart)*xx/duration, yy);
    ctx.limeTo((now - timeStart)*xx/duration, yy - 3*amp);
    ctx.strokeStyle = "red";
    ctx.lineWidth = 3;
    ctx.stroke();
}

// Call the function to draw the curve
drawCurve();
