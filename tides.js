function drawCurve() {
    const canvas = document.getElementById('myCanvas');
    const ctx = canvas.getContext('2d');
    const amp = 100; // amplitude
    const xx = canvas.width;
    const yy = canvas.height;

    const currentDate = new Date();
    const timeStart = currentDate.getTime();
    const duration = 2 * 24*3600*1000;  //two days hence
    const timeEnd = timeStart + duration;
    
    ctx.clearRect(0, 0, xx, yy);
    ctx.beginPath();
    ctx.rect(0, yy, xx, yy - 3*amp);
    ctx.rect(0, yy - amp, xx, yy - 2*amp);
    ctx.strokeStyle = '#111';
    ctx.stroke();

    
    ctx.beginPath();
    for (let x = timeStart; x < timeEnd; x += 1*3600) {
      const y = amp*tideHeight(x);
      ctx.lineTo((x - timeStart)*xx/duration, yy - y);
    }
    ctx.strokeStyle = '#000';
    ctx.stroke();
}

// Call the function to draw the curve
drawCurve();
