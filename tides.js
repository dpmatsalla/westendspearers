function drawCurve() {
    const canvas = document.getElementById('myCanvas');
    const ctx = canvas.getContext('2d');
    const amplitude = 50; // Change this value to adjust the height of the curve

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.beginPath();

    const currentDate = new Date();
    const timeStart = currentDate.getTime();
    const duration = 2 * 24*3600*1000;  //two days hence
    const timeEnd = timeStart + duration;
    
    ctx.moveTo(0,canvas.height);
    ctx.lineTo(canvas.width,canvas.height);
    ctx.strokeStyle = '#111';
    ctx.stroke();

    for (let x = timeStart; x < timeEnd; x += 1*3600) {
      const y = canvas.height - amplitude * tideHeight(x);
      ctx.lineTo((x - timeStart)*canvas.width/duration, y);
    }

    ctx.strokeStyle = '#000';
    ctx.stroke();
}

// Call the function to draw the curve
drawCurve();
